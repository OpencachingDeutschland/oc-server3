<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 ****************************************************************************/

namespace Oc\Frontend\Login;

use Behat\Mink\Element\NodeElement;
use Oc\Frontend\AbstractFrontendTest;

class FieldNotesTest extends AbstractFrontendTest
{
    private function cleanUpFieldNotesTable()
    {

    }

    /**
     * @group frontend
     * @group frontend-account
     * @group frontend-account-fieldNotes
     *
     * @return void
     */
    public function testFieldNotesWithWrongFileFormat()
    {
        $this->login();
        $this->session->visit($this->baseUrl . '/field-notes/');
        $page = $this->session->getPage();
        $page->attachFileToField(
            'upload_field_notes[file]',
            __DIR__ . '/../../fixtures/FieldNotes/fieldnotes_wrong_file_format.txt'
        );

        $page->pressButton('testing-fieldNotes-submit-button');
        $errorMessage = $page->find('css', '.alert-danger');
        self::assertEquals('This file seems not to be a field notes file.', $errorMessage->getText());
    }

    /**
     * @group frontend
     * @group frontend-account
     * @group frontend-account-fieldNotes
     *
     * @return void
     */
    public function testFieldNotesWithWrongDateFormat()
    {
        $this->login();
        $this->session->visit($this->baseUrl . '/field-notes/');
        $page = $this->session->getPage();
        $page->attachFileToField(
            'upload_field_notes[file]',
            __DIR__ . '/../../fixtures/FieldNotes/fieldnotes_wrong_date_format.txt'
        );

        $page->pressButton('testing-fieldNotes-submit-button');
        $errorMessage = $page->find('css', '.alert-danger');
        self::assertEquals('The date in this file is in wrong format.', $errorMessage->getText());
    }

    /**
     * @group frontend
     * @group frontend-account
     * @group frontend-account-fieldNotes
     *
     * @return void
     */
    public function testFieldNotesImport()
    {
        $this->login();
        $this->session->visit($this->baseUrl . '/field-notes/');
        $page = $this->session->getPage();
        $page->attachFileToField(
            'upload_field_notes[file]',
            __DIR__ . '/../../fixtures/FieldNotes/fieldnotes_working.txt'
        );

        $page->pressButton('testing-fieldNotes-submit-button');
        $errorMessage = $page->find('css', '.flash-messages');
        self::assertEquals('Geocache "﻿OC9CC1" not found. Geocache "OC10FC7" not found.', $errorMessage->getText());

        /** @var array $fieldNotesRow */
        $fieldNotesRow[1] = $page->find('css', '.testing-fieldNotes-rows1');
        $fieldNotesRow[2] = $page->find('css', '.testing-fieldNotes-rows2');
        $fieldNotesRow[3] = $page->find('css', '.testing-fieldNotes-rows3');

        $fieldNotesResult[1] = 'TestcacheÖäü 2016-05-14 15:13 Found Log it | Delete';
        $fieldNotesResult[2] = 'Naafbachtal II DEF 456 2016-06-02 18:05 Not found Log it | Delete';
        $fieldNotesResult[3] = 'Testcache111 222 2016-06-02 18:06 Needs maintainance Log it | Delete';

        /** @var NodeElement $fieldNote */
        foreach ($fieldNotesRow as $key => $fieldNote) {
            self::assertEquals($fieldNotesResult[$key], $fieldNote->getText());
        }

        $page->checkField('testing-fieldNote-checkbox1');
        $page->checkField('testing-fieldNote-checkbox2');
        $page->checkField('testing-fieldNote-checkbox3');
        $page->pressButton('Delete selected');

        $errorMessage = $page->find('css', '.flash-messages');
        self::assertEquals('Field Notes successfully deleted.', $errorMessage->getText());
    }
}
