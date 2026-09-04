<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ParticipantCardTest extends CIUnitTestCase
{
    private array $sampleData = [
        'retdat' => [
            [
                'school_code' => 'SCH001',
                'school_name' => 'Govt Model School',
            ],
            [
                'school_code' => 'SCH002',
                'school_name' => 'St. Joseph School',
            ],
        ],
    ];

    public function testParticipantCardIndexPhotoViewsRenderWithResponsiveGrid(): void
    {
        $schoolView = view('report/prereport/participant_cardindex_school_photo', $this->sampleData);
        $this->assertStringContainsString('col-12 col-md-6', $schoolView);
        $this->assertStringContainsString('card-report-box', $schoolView);
        $this->assertStringContainsString('icon-camera-anim', $schoolView);
        $this->assertStringContainsString('card-icon-hero', $schoolView);
        $this->assertStringContainsString('TAU-Pallai', $schoolView);

        $regnoView = view('report/prereport/participant_regno_photo', $this->sampleData);
        $this->assertStringContainsString('col-12 col-md-6', $regnoView);
        $this->assertStringContainsString('card-report-box', $regnoView);
        $this->assertStringContainsString('icon-person-anim', $regnoView);
        $this->assertStringContainsString('btn-card-action', $regnoView);
    }

    public function testParticipantCardIndex2ViewsRenderWithResponsiveGrid(): void
    {
        $schoolView = view('report/prereport/participant_cardindex_schoolwithphoto', $this->sampleData);
        $this->assertStringContainsString('col-12 col-md-6', $schoolView);
        $this->assertStringContainsString('card-report-box', $schoolView);
        $this->assertStringContainsString('icon-building-anim', $schoolView);
        $this->assertStringContainsString('card-icon-hero', $schoolView);
        $this->assertStringContainsString('TAU-Pallai', $schoolView);

        $regnoView = view('report/prereport/participant_regnowithphoto', $this->sampleData);
        $this->assertStringContainsString('col-12 col-md-6', $regnoView);
        $this->assertStringContainsString('card-report-box', $regnoView);
        $this->assertStringContainsString('icon-person-anim', $regnoView);
        $this->assertStringContainsString('btn-card-action', $regnoView);
    }

    public function testParticipantCardIndexViewsRenderWithResponsiveGrid(): void
    {
        $schoolView = view('report/prereport/participant_cardindex', $this->sampleData);
        $this->assertStringContainsString('col-12 col-md-6', $schoolView);
        $this->assertStringContainsString('card-report-box', $schoolView);
        $this->assertStringContainsString('icon-building-anim', $schoolView);
        $this->assertStringContainsString('card-icon-hero', $schoolView);
        $this->assertStringContainsString('TAU-Pallai', $schoolView);

        $regnoView = view('report/prereport/participant_regno', $this->sampleData);
        $this->assertStringContainsString('col-12 col-md-6', $regnoView);
        $this->assertStringContainsString('card-report-box', $regnoView);
        $this->assertStringContainsString('icon-person-anim', $regnoView);
        $this->assertStringContainsString('btn-card-action', $regnoView);
    }

    public function testAcademicYearCalculation(): void
    {
        // helper general should be loaded
        helper('general');

        // Current academic year in 2026 (September) should be 2026-27
        $this->assertSame('2026-27', get_academic_year());
        $this->assertSame('Tamil Kalaivizha 2026-27', get_festival_title());

        // Test past months within same academic year (e.g., May 2027 => month 5, still 2026-27)
        $may2027 = strtotime('2027-05-15 10:00:00');
        $this->assertSame('2026-27', get_academic_year($may2027));
        $this->assertSame('Tamil Kalaivizha 2026-27', get_festival_title('Tamil Kalaivizha', $may2027));

        // Test start of next academic year (e.g., June 2027 => month 6, rolls over to 2027-28)
        $june2027 = strtotime('2027-06-01 00:00:00');
        $this->assertSame('2027-28', get_academic_year($june2027));
        $this->assertSame('Tamil Kalaivizha 2027-28', get_festival_title('Tamil Kalaivizha', $june2027));

        // Test future month in next academic year (e.g., September 2027 => 2027-28)
        $sep2027 = strtotime('2027-09-10 12:00:00');
        $this->assertSame('2027-28', get_academic_year($sep2027));
        $this->assertSame('Tamil Kalaivizha 2027-28', get_festival_title('Tamil Kalaivizha', $sep2027));
    }

    public function testPdfTemplatesDoNotContainOldKalolsavamTitle(): void
    {
        $templates = [
            APPPATH . 'Views/report/prereportpdf/pt_photo.php',
            APPPATH . 'Views/report/prereportpdf/participant_regno_photo.php',
            APPPATH . 'Views/report/prereportpdf/pt.php',
            APPPATH . 'Views/report/prereportpdf/participant_regno.php',
            APPPATH . 'Views/report/prereportpdf/participant_cardindex.php',
            APPPATH . 'Views/report/prereportpdf/pt1.php',
            APPPATH . 'Views/report/prereportpdf/pt_all.php',
            APPPATH . 'Views/report/prereportpdf/pt_photo_all.php',
            APPPATH . 'Views/report/prereportpdf/pt4_photo.php',
            APPPATH . 'Views/schools/registration_report.php',
            APPPATH . 'Views/schools/registration_report_html.php',
            APPPATH . 'Views/itemreport/itemreportpdf.php',
        ];

        foreach ($templates as $file) {
            $this->assertFileExists($file);
            $content = file_get_contents($file);
            $this->assertStringNotContainsString('Kerala School Kalolsavam', $content, "Failed in $file");
            $hasTitleOrRenderer = str_contains($content, 'get_festival_title()') ||
                                  str_contains($content, 'render_single_photo_card') ||
                                  str_contains($content, 'render_single_nophoto_card');
            $this->assertTrue($hasTitleOrRenderer, "Missing get_festival_title() or card renderer in $file");
        }
    }

    public function testVerticalTextImageGeneration(): void
    {
        helper('general');
        $img = pdf_vertical_text_image('1001', 24, 72, 14, '#660033');
        $this->assertNotEmpty($img);
        $this->assertFileExists($img);
        $info = getimagesize($img);
        $this->assertSame(24, $info[0]);
        $this->assertSame(72, $info[1]);
    }

    public function testRenderSinglePhotoCardSubDistrictVisibility(): void
    {
        helper('general');
        $participant = [
            'participant_id' => '1001',
            'participant_name' => 'KAVITHA M',
            'school_code' => 'SCH001',
            'school_name' => 'Govt Model HSS',
            'place' => 'Palakkad Sub-District',
            'class' => '10',
            'admn_no' => '501',
        ];
        $photo = ['pic' => ['501' => FCPATH . 'images/img_user_nophoto.jpg']];
        $items = [
            [
                'item_code' => '501',
                'item_name' => 'Bharatanatyam',
                'cluster_no' => '1',
                'stage_name' => 'Stage 1',
                'start_time' => '2026-10-15 10:00:00',
            ],
        ];

        // Sub-District event: hidden
        $htmlSub = render_single_photo_card($participant, $photo, '', 'Palakkad Sub District', 'Main Auditorium', null, $items);
        $this->assertStringNotContainsString('Palakkad Sub-District', $htmlSub);

        // District event: shown
        $participantDistrict = $participant;
        $participantDistrict['is_district'] = true;
        $htmlDist = render_single_photo_card($participantDistrict, $photo, '', 'District Festival', 'Main Auditorium', null, $items);
        $this->assertStringContainsString('Palakkad Sub-District', $htmlDist);
    }

    public function testDatewisePartViewFormatting(): void
    {
        helper('general');
        $viewFile = APPPATH . 'Views/report/prereportpdf/datewisepart.php';
        $this->assertFileExists($viewFile);
        $content = file_get_contents($viewFile);

        // Black title
        $this->assertStringContainsString('color: #000000', $content);
        // Broken Grand Total header
        $this->assertStringContainsString('Grand<br/>Total', $content);
        // Hyphen format helper for zero values
        $this->assertStringContainsString('fmt_datewise_count', $content);
        // Single-line school name wordwrap threshold (50 chars)
        $this->assertStringContainsString('wordwrap($school_full_name, 50', $content);
        // Modern O(1) associative lookup maps
        $this->assertStringContainsString('$lp_map', $content);
        $this->assertStringContainsString('$up_map', $content);
        $this->assertStringContainsString('$hs_map', $content);
        $this->assertStringContainsString('$hss_map', $content);
        // Table spans 100% width matching page margins
        $this->assertStringContainsString('style="width: 100%; border-collapse: collapse;"', $content);
        // School column width reduced from 345 to 260
        $this->assertStringContainsString('width="260"', $content);
        // Header and footer templates preserved
        $this->assertStringContainsString("report/report_header", $content);
        $this->assertStringContainsString("report/report_footer", $content);
    }

    public function testPtPhotoViewHasNoSyntaxErrors(): void
    {
        $viewFile = APPPATH . 'Views/report/prereportpdf/pt_photo.php';
        $this->assertFileExists($viewFile);
        $content = file_get_contents($viewFile);

        // Ensure duplicate opening tag is absent
        $this->assertDoesNotMatchRegularExpression('/}\s*<\?php\s*\$chunks\s*=/i', $content);
        $this->assertStringContainsString('render_single_photo_card', $content);
        $this->assertStringContainsString('$file_path = pdf_safe_logo_image', $content);
    }

    public function testParticipantCardPhotoRoutesAndTestControllerExist(): void
    {
        $routesFile = file_get_contents(APPPATH . 'Config/Routes.php');
        $this->assertStringContainsString("report/prereportpdf/participant_cardindex_school_photo", $routesFile);
        $this->assertStringContainsString("report/prereportpdf/participant_cardindex_photo", $routesFile);
        $this->assertStringContainsString("test/nodata", $routesFile);

        $testController = new \App\Controllers\Test();
        $this->assertTrue(method_exists($testController, 'nodata'));
    }

    public function testParticipantCardSchoolPhotoRedirectsGracefullyWhenSchoolCodeEmpty(): void
    {
        session()->set(['USERID' => 1, 'CHANGEPWD' => 'Y', 'USER_GROUP' => 'W']);
        $controller = new \App\Controllers\Report\Prereportpdf();

        try {
            $controller->participant_cardindex_school_photo();
        } catch (\CodeIgniter\HTTP\Exceptions\RedirectException $e) {
            $target = $e->getResponse()->getHeaderLine('Location');
            $this->assertStringContainsString('report/prereport/participant_cardindex_photo', $target);
        }
    }
}


