<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_cohortrole;

use advanced_testcase;
use context_system;

/**
 * Unit tests for event observers
 *
 * @package    local_cohortrole
 * @covers     \local_cohortrole\observers
 * @covers     \local_cohortrole\persistent
 * @copyright  2018 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class observers_test extends advanced_testcase {

    /** @var \local_cohortrole\persistent $persistent. */
    protected $persistent;

    /**
     * Load required test libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        parent::setUpBeforeClass();

        require_once("{$CFG->dirroot}/cohort/lib.php");
    }

    /**
     * Test setup
     */
    protected function setUp(): void {
        parent::setUp();

        $this->resetAfterTest(true);

        // Create test role/cohort.
        $roleid = $this->getDataGenerator()->create_role();
        $cohort = $this->getDataGenerator()->create_cohort();

        // Link them together.
        $this->persistent = $this->getDataGenerator()->get_plugin_generator('local_cohortrole')
            ->create_persistent(['roleid' => $roleid, 'cohortid' => $cohort->id]);
    }

    /**
     * Tests cohort_deleted event observer
     */
    public function test_cohort_deleted(): void {
        $context = context_system::instance();

        $user = $this->getDataGenerator()->create_user();
        cohort_add_member($this->persistent->get('cohortid'), $user->id);

        $userhasrole = user_has_role_assignment($user->id, $this->persistent->get('roleid'), $context->id);
        $this->assertTrue($userhasrole);

        cohort_delete_cohort($this->persistent->get_cohort());

        // User should not be assigned to the test role.
        $userhasrole = user_has_role_assignment($user->id, $this->persistent->get('roleid'), $context->id);
        $this->assertFalse($userhasrole);

        // Ensure plugin tables are cleaned up.
        $exists = $this->persistent->record_exists_select('cohortid = ?', [$this->persistent->get('cohortid')]);
        $this->assertFalse($exists);
    }

    /**
     * Tests cohort_member_added event observer
     */
    public function test_cohort_member_added(): void {
        $context = context_system::instance();

        $user = $this->getDataGenerator()->create_user();

        $userhasrole = user_has_role_assignment($user->id, $this->persistent->get('roleid'), $context->id);
        $this->assertFalse($userhasrole);

        cohort_add_member($this->persistent->get('cohortid'), $user->id);

        // User should be assigned to the test role.
        $userhasrole = user_has_role_assignment($user->id, $this->persistent->get('roleid'), $context->id);
        $this->assertTrue($userhasrole);
    }

    /**
     * Tests cohort_member_removed event observer
     */
    public function test_cohort_member_removed(): void {
        $context = context_system::instance();

        $user1 = $this->getDataGenerator()->create_user();
        cohort_add_member($this->persistent->get('cohortid'), $user1->id);

        $user2 = $this->getDataGenerator()->create_user();
        cohort_add_member($this->persistent->get('cohortid'), $user2->id);

        cohort_remove_member($this->persistent->get('cohortid'), $user1->id);

        // User 2 should be assigned to the test role, user 1 should not.
        $userhasrole = user_has_role_assignment($user2->id, $this->persistent->get('roleid'), $context->id);
        $this->assertTrue($userhasrole);

        $userhasrole = user_has_role_assignment($user1->id, $this->persistent->get('roleid'), $context->id);
        $this->assertFalse($userhasrole);
    }

    /**
     * Tests role_deleted event observer
     */
    public function test_role_deleted(): void {
        delete_role($this->persistent->get('roleid'));

        // Ensure plugin tables are cleaned up.
        $exists = $this->persistent->record_exists_select('roleid = ?', [$this->persistent->get('roleid')]);
        $this->assertFalse($exists);
    }
}
