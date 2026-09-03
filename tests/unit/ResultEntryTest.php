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
                    'item_name' => 'Test Item',
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
        $this->assertStringContainsString('Test Item', $rendered);
        $this->assertStringContainsString('Number of Judges', $rendered);
    }
}
