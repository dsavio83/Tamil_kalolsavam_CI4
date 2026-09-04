<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ResultEntryTest extends CIUnitTestCase
{
    public function testResultEntryViewRendersWithoutVariables(): void
    {
        $rendered = view('result/resultentry', []);
        $this->assertStringContainsString('Result Entry', $rendered);
        $this->assertStringContainsString('txtItemCode', $rendered);
    }

    public function testResultEntryViewRendersWithVariables(): void
    {
        $data = [
            'selected_item' => [
                [
                    'item_code' => '101',
                    'item_name' => 'Bharathanatyam',
                    'total_participants' => 5,
                    'no_of_judges' => 3
                ]
            ],
            'selected_item_list' => [],
            'selected_result' => [],
            'show_conirm_button' => 'yes',
            'absentee_list' => false,
            'add_edit' => 'yes',
        ];
        $rendered = view('result/resultentry', $data);
        $this->assertStringContainsString('Bharathanatyam', $rendered);
        $this->assertStringContainsString('Number of Judges', $rendered);
        $this->assertStringContainsString('Enter Participant Score', $rendered);
    }

    public function testResultEntryViewRendersWithResultsTable(): void
    {
        $data = [
            'selected_item' => [
                [
                    'item_code' => '101',
                    'item_name' => 'Bharathanatyam',
                    'total_participants' => 3,
                    'no_of_judges' => 3
                ]
            ],
            'selected_item_list' => [
                [
                    'rm_id' => '1',
                    'participant_id' => '501',
                    'code_no' => '101',
                    'marks' => '28#$#29#$#30',
                    'total_mark' => '87',
                    'percentage' => '87.00',
                    'rank' => '1',
                    'grade' => 'A',
                    'point' => '5',
                    'is_withheld' => 'N'
                ],
                [
                    'rm_id' => '2',
                    'participant_id' => '502',
                    'code_no' => '102',
                    'marks' => '26#$#27#$#28',
                    'total_mark' => '81',
                    'percentage' => '81.00',
                    'rank' => '2',
                    'grade' => 'A',
                    'point' => '5',
                    'is_withheld' => 'N'
                ]
            ],
            'selected_result' => [],
            'show_conirm_button' => 'yes',
            'absentee_list' => false,
            'add_edit' => 'yes',
        ];
        $rendered = view('result/resultentry', $data);
        $this->assertStringContainsString('Results Roster', $rendered);
        $this->assertStringContainsString('1st', $rendered);
        $this->assertStringContainsString('2nd', $rendered);
        $this->assertStringContainsString('Confirm Results', $rendered);
    }

    public function testEditResultEntryViewRendersEditMode(): void
    {
        $data = [
            'selected_item' => [
                [
                    'item_code' => '101',
                    'item_name' => 'Bharathanatyam',
                    'total_participants' => 3,
                    'no_of_judges' => 2
                ]
            ],
            'selected_item_list' => [
                [
                    'rm_id' => '1',
                    'participant_id' => '501',
                    'code_no' => '101',
                    'marks' => '28#$#29',
                    'total_mark' => '57',
                    'percentage' => '57.00',
                    'rank' => '1',
                    'grade' => 'C',
                    'point' => '1',
                    'is_withheld' => 'N'
                ]
            ],
            'selected_result' => [
                [
                    'rm_id' => '1',
                    'participant_id' => '501',
                    'code_no' => '101',
                    'marks' => '28#$#29',
                    'total_mark' => '57'
                ]
            ],
            'show_conirm_button' => 'yes',
            'absentee_list' => false,
            'add_edit' => 'yes',
        ];
        $rendered = view('result/resultentry', $data);
        $this->assertStringContainsString('Edit Participant Score', $rendered);
        $this->assertStringContainsString('Update', $rendered);
        $this->assertStringContainsString('Cancel', $rendered);
        $this->assertStringContainsString('value="101"', $rendered); // Code No in edit input
        $this->assertStringContainsString('value="501"', $rendered); // Reg No in edit input
        $this->assertStringContainsString('target="_self"', $rendered);
        $this->assertStringContainsString('id="result_row_1"', $rendered);
        $this->assertStringContainsString('data-rm-id="1"', $rendered);
        $this->assertStringContainsString('data-participant-id="501"', $rendered);
        $this->assertStringContainsString('data-code-no="101"', $rendered);
        $this->assertStringContainsString('data-marks="28#$#29"', $rendered);
        $this->assertStringContainsString('data-total-mark="57"', $rendered);
        $this->assertStringContainsString('id="hidCntItemList"', $rendered);
        $this->assertStringContainsString('id="hidTotalPart"', $rendered);
        $this->assertStringContainsString('consoleEl.scrollIntoView', $rendered);
    }

    public function testResultEntryInputIsClearedWhenItemLoaded(): void
    {
        $data = [
            'item_code' => '101',
            'selected_item' => [
                [
                    'item_code' => '101',
                    'item_name' => 'Bharathanatyam',
                    'total_participants' => 5,
                    'no_of_judges' => 3
                ]
            ],
            'selected_item_list' => [],
            'selected_result' => [],
            'show_conirm_button' => 'yes',
            'absentee_list' => false,
            'add_edit' => 'yes',
        ];
        $rendered = view('result/resultentry', $data);
        // txtItemCode should render with empty value attribute
        $this->assertStringContainsString('id="txtItemCode" placeholder="Item Code (e.g. 101)"', $rendered);
        $this->assertStringContainsString('value=""', $rendered);
        // Hidden input keeps the loaded item code
        $this->assertStringContainsString('id="hidItemId" value="101"', $rendered);
    }

    public function testResultEntryRoutesConfigured(): void
    {
        $routes = service('routes');
        $routes->loadRoutes();
        $definedRoutes = $routes->getRoutes('GET');

        // Check GET routes
        $this->assertArrayHasKey('result/resultentry', $definedRoutes);
        $this->assertArrayHasKey('result/resultentry/index', $definedRoutes);
        $this->assertArrayHasKey('result/resultentry/get_item_details_result', $definedRoutes);
        $this->assertArrayHasKey('result/resultentry/save_result_entry', $definedRoutes);
        $this->assertArrayHasKey('result/resultentry/edit_result_entry', $definedRoutes);
        $this->assertArrayHasKey('result/resultentry/delete_result_entry', $definedRoutes);
        $this->assertArrayHasKey('result/resultentry/confirm_result_entry', $definedRoutes);
        $this->assertArrayHasKey('result/resultentry/printConfidentialReport', $definedRoutes);
        $this->assertArrayHasKey('result/resultentry/item_result_list', $definedRoutes);

        // Check POST routes
        $postRoutes = $routes->getRoutes('POST');
        $this->assertArrayHasKey('result/resultentry', $postRoutes);
        $this->assertArrayHasKey('result/resultentry/edit_result_entry', $postRoutes);
        $this->assertArrayHasKey('result/resultentry/delete_result_entry', $postRoutes);
        $this->assertArrayHasKey('result/resultentry/save_result_entry', $postRoutes);
        $this->assertArrayHasKey('result/resultentry/confirm_result_entry', $postRoutes);
    }

    public function testFestivalTypeListRendersFullWidthSingleRowCard(): void
    {
        $data = [
            'fest' => [
                '1' => 'LP General',
                '2' => 'UP General',
                '3' => 'HS General',
            ]
        ];
        $rendered = view('result/festival_type_list', $data);
        $this->assertStringContainsString('Select Festival Category', $rendered);
        $this->assertStringContainsString('View event-wise completion and confirmation status', $rendered);
        $this->assertStringContainsString('fest-hero-icon', $rendered);
        $this->assertStringContainsString('fest-pill-box', $rendered);
        $this->assertStringContainsString('btn-fest-load', $rendered);
        $this->assertStringContainsString('fest-spin-icon', $rendered);
        $this->assertStringContainsString('Load Status', $rendered);
        $this->assertStringContainsString('cmbFestType', $rendered);
        $this->assertStringContainsString('formIRL', $rendered);
        $this->assertStringContainsString('TAU-Pallai', $rendered);
        $this->assertStringContainsString('linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%)', $rendered);
    }
}
