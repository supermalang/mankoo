<?php

namespace App\Controller;

use App\Controller\Admin\MemberCrudController;
use App\Entity\Member;
use App\Entity\Section;
use App\Form\MemberImportFileType;
use App\Service\FileUploader;
use App\Service\SpreadsheetHelper;
use App\Service\StringHelper;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WorksheetReadFilter;

define('MAX_ROWS_TO_LOAD', 10000);
define('DB_INSERT_BATCH_SIZE', 100);

class ImportMemberController extends AbstractController
{
    private $adminUrlGenerator;
    private $security;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, Security $security)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->security = $security;
    }

    #[Route('/admin/member/importfile', name: 'app_members_importfile')]
    public function new(Request $request, FileUploader $fileUploader)
    {
        $form = $this->createForm(MemberImportFileType::class);
        $form->handleRequest($request);

        $supportedFileExtensions = ['csv', 'xlsx', 'xls'];

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $importmembersFile */
            $importmembersFile = $form->get('importmembers')->getData();

            if ($importmembersFile) {
                $importmembersFileName = $fileUploader->upload($importmembersFile, $supportedFileExtensions);
            }

            $url = $this->adminUrlGenerator
                ->setRoute('app_members_readimportedfile', [
                    'importmembersFileName' => $importmembersFileName,
                ])
                ->generateUrl()
            ;

            return $this->redirect($url);
        }

        return $this->renderForm('import_members/new.html.twig', [
            'controller_name' => 'ImportMemberController',
            'page_name' => 'Import Member',
            'form' => $form,
        ]);
    }

    #[Route('/admin/member/readimportedfile', name: 'app_members_readimportedfile')]
    public function readImportedFileAction(Request $request, SpreadsheetHelper $sprdHelper)
    {
        $uploadedMemberFileDir = $this->getParameter('importmembers_directory');
        $importedFile = $request->query->all()['routeParams']['importmembersFileName'];
        $importedfile_fullpath = $uploadedMemberFileDir.'/'.$importedFile;

        if (!file_exists($importedfile_fullpath)) {
            throw new FileNotFoundException('File not found: '.$importedfile_fullpath);
        }

        $worksheetRows = $sprdHelper->getWorksheetRows($importedfile_fullpath);

        $worksheetRowsToLoad = $worksheetRows <= 10 ? $worksheetRows : 10;

        $filterSubset = new WorksheetReadFilter(1, $worksheetRowsToLoad, range('A', 'F'));
        $spreadsheet = $sprdHelper->readFile($importedfile_fullpath, filterSubset: $filterSubset);
        $worksheet = $sprdHelper->getWorksheet($spreadsheet);
        $data = $sprdHelper->createDataFromWorksheet($worksheet);

        $redirectUrl = $this->generateUrl('app_members_importmembers_persist', [
            'importedFile' => $importedFile,
        ]);

        $cancelUrl = $this->adminUrlGenerator
            ->setController(MemberCrudController::class)
            ->set('todelete', $importedFile)
            ->generateUrl()
            ;

        return $this->render('import_members/preview.html.twig', [
            'data' => $data,
            'page_name' => 'Preview of imported file',
            'redirect_url' => $redirectUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }

    #[Route('/admin/persistimportedfile', name: 'app_members_importmembers_persist')]
    public function persistImportedMembersAction(Request $request, SpreadsheetHelper $sprdHelper, StringHelper $strngHelper, ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
        $uploadedMemberFileDir = $this->getParameter('importmembers_directory');
        $importedFile = $request->query->all()['importedFile'];
        $importedfile_fullpath = $uploadedMemberFileDir.'/'.$importedFile;

        if (!file_exists($importedfile_fullpath)) {
            throw new FileNotFoundException('File not found: '.$importedfile_fullpath);
        }

        $worksheetRows = $sprdHelper->getWorksheetRows($importedfile_fullpath);

        $worksheetRowsToLoad = $worksheetRows <= MAX_ROWS_TO_LOAD ? $worksheetRows : MAX_ROWS_TO_LOAD;

        $filterSubset = new WorksheetReadFilter(1, $worksheetRowsToLoad, range('A', 'F'));
        $spreadsheet = $sprdHelper->readFile($importedfile_fullpath, filterSubset: $filterSubset);
        $worksheet = $sprdHelper->getWorksheet($spreadsheet);
        $data = $sprdHelper->createDataFromWorksheet($worksheet);

        $entityPropertyMap = [
            'First Name' => 'firstName',
            'Last Name' => 'lastName',
            'Telephone 1' => 'telephone1',
            'Telephone 2' => 'telephone2',
            'Address' => 'address',
            'Section' => 'section',
        ];

        // This array will be used to keep order of columns as in the imported file
        $data['entityPropertyMap'] = [];

        foreach ($data['columnNames'] as $key => $col) {
            $col = $strngHelper->mytrim($col); // The trim() not really working in some cases here because of unicode

            if (array_key_exists($col, $entityPropertyMap)) {
                $data['entityPropertyMap'][$key] = $entityPropertyMap[$col];
            }
        }

        $em = $doctrine->getManager();
        $entityValidationErrors = [];
        $nbEntitiesCreated = 0;
        $nbEntitiesUpdated = 0;

        // Create or update entities
        foreach ($data['columnValues'] as $rowkey => $row) {
            $member = new Member();

            foreach ($row as $colkey => $col) {
                $colname = $data['entityPropertyMap'][$colkey];
                $setter = 'set'.ucfirst($colname);

                if ('section' === $colname) {
                    $section = $em->getRepository(Section::class)->findOneBy(['label' => $col]);

                    if ($section) {
                        $member->{$setter}($section);
                    }

                    continue;
                }

                $member->{$setter}($col);

                if (method_exists($member, 'setCreated')) {
                    $member->setCreated(new \DateTimeImmutable());
                }

                if (method_exists($member, 'setCreatedBy')) {
                    $member->setCreatedBy($this->security->getUser());
                }
            }

            $em->persist($member);

            $errors = $validator->validate($member);

            // If there are no errors, increase the counter of entities created
            $nbEntitiesCreated = count($errors) > 0 ? $nbEntitiesCreated : $nbEntitiesCreated + 1;

            // If there are validation errors:
            // 1. Check whether we need to update an existing entity
            // 2. If not, add them to the array of errors, to be displayed later
            foreach ($errors as $key => $error) {
                $em->detach($member);
                $propertyPath = $error->getPropertyPath();

                // Check if the error is happening because of an entity that already exists (unique constraint, etc.)
                $existing_entity = $em->getRepository(Member::class)->findOneBy([$propertyPath => $error->getInvalidValue()]);

                // The entity already exists, so we need to update the existing entity
                if ($existing_entity && !null == $error->getInvalidValue()) {
                    $createDate = $existing_entity->getCreated();
                    $createdBy = $existing_entity->getCreatedBy();

                    $existing_entity = $member;

                    // Set the created date and created by of the existing entity (Should be same as the one we are trying to update)
                    $existing_entity->setCreated($createDate);
                    $existing_entity->setCreatedBy($createdBy);

                    $nbEntitiesUpdated = $nbEntitiesUpdated + 1;

                    continue;
                }

                $errorMessageTemplate = $error->getMessage();
                $errorMessage = str_replace('{{ label }}', $propertyPath, $errorMessageTemplate);
                $errorMessage = str_replace('This value', "The '".$propertyPath."' value", $errorMessageTemplate);

                array_push($entityValidationErrors, [
                    'Row #' => $rowkey,
                    'First Name' => $member->getFirstName() ?? '',
                    'Last Name' => $member->getLastName() ?? '',
                    'Telephone 1' => $member->getTelephone1() ?? '',
                    'Telephone 2' => $member->getTelephone2() ?? '',
                    'Address' => $member->getAddress() ?? '',
                    'Section' => $member->getSection() ?? '',
                    'Error message' => $errorMessage,
                ]);
            }

            if (($rowkey % DB_INSERT_BATCH_SIZE) === 0) {
                $em->flush();
                $em->clear(); // Detaches all objects from Doctrine!
            }
        }
        $em->flush(); // Persist objects that did not make up an entire batch
        $em->clear();

        $filesystem = new Filesystem();
        $filesystem->remove($importedfile_fullpath);

        $redirectUrl = $this->adminUrlGenerator
            ->setRoute('app_members_importmembers_showresults', [
                'created' => $nbEntitiesCreated,
                'updated' => $nbEntitiesUpdated,
                'errors' => $entityValidationErrors,
            ])
            ->generateUrl()
            ;

        return $this->redirect($redirectUrl);
    }

    #[Route('/admin/showimportedmembersresults', name: 'app_members_importmembers_showresults')]
    public function showImportResultsAction(Request $request)
    {
        $created = $request->query->all()['routeParams']['created'];
        $updated = $request->query->all()['routeParams']['updated'];
        $errors = $request->query->all()['routeParams']['errors'];

        return $this->render('import_members/result.html.twig', [
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
            'page_name' => 'Member import',
        ]);
    }
}
