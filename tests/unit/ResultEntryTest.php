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
}
