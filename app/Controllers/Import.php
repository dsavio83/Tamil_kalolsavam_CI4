<?php

namespace App\Controllers;


class Import  extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->load->model('Session_Model');
        //$this->load->model('General_Model');
        //$this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/admin/admin.js');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->load->library('csvreader');
        $this->load->model('Import_Model');
        $this->Contents = array();
    }

    function index() {
        ?>
        <a href="<?php echo base_url(); ?>login/logout" title="Log out of this account">Logout</a>
        <?php

        $this->import_data();
    }

    function import_data() {
        if ($this->session->get('USER_GROUP') == 'A' && ($this->session->get('USER_TYPE') == 3 || $this->session->get('USER_TYPE') == 2)) {

            if (is_import_data_finish($this->session->get('SUB_DISTRICT'))) {
                $this->Contents['import_completed'] = 'YES';
                $this->template->write_view('menu', 'menu', '');
            } else {
                $this->Contents['import_completed'] = 'NO';
            }
            if (isset($_FILES['kalolsavamCSV']['name']) && !empty($_FILES['kalolsavamCSV']['name'])) {
                $file_name = $this->General_Model->upload_kalolsavam_csv_data('kalolsavamCSV', 'kalolsavamCSV' . substr(fncUuid(), 10));
               
                
                if (!$file_name) {
                    $this->Contents['upload_error'] = 'Invalid CSV file. Only .csv text files up to 8 MB are accepted.';
                    $this->template->write('error', $this->Contents['upload_error']);
                    $this->template->write_view('content', 'import/import_csv_data', $this->Contents);
                    $this->template->load();
                    return;
                }
                if ($this->session->get('USER_TYPE') == 3) {
                    $import_result = $this->Import_Model->insert_import_data($file_name);
                    //   echo "<br>";
                    //   var_dump($import_result); die;
                    if (TRUE === $import_result) {
                        $this->Contents['import_completed'] = 'YES';
                        $this->template->write_view('menu', 'menu', '');
                        $this->template->write('message', 'CSV Data saved successfully');
                    } else if ('INVALID_DATA' == $import_result) {
                        $this->template->write('error', 'Invalid CSV');
                    } else if ('DATA_ALREADY_ENTERED' == $import_result) {
                        $this->template->write('error', 'CSV data already imported');
                    } else {
                        $this->template->write('error', 'Failed to Save CSV Data');
                    }
                } else if ($this->session->get('USER_TYPE') == 2) {
                    $import_result = $this->Import_Model->insert_import_district_data($file_name);
                    if (TRUE === $import_result) {
                        $this->template->write('message', 'CSV Data saved successfully');
                    } else if ('INVALID_SUB_DISRICT_DATA' == $import_result) {
                        $this->template->write('error', 'This Sub-District is not belongs to current District');
                    } else if ('DATA_ALREADY_ENTERED' == $import_result) {
                        $this->template->write('error', 'Sub-District data already imported');
                    } else {
                        $this->template->write('error', 'Failed to Save CSV Data');
                    }
                }
            }

            $this->template->write_view('content', 'import/import_csv_data', $this->Contents);
            $this->template->load();
        } else
            redirect('welcome');
    }

    function import_district_kalolsavam_data() {
        if ($this->session->get('USER_GROUP') == 'A' && $this->session->get('USER_TYPE') == 2) {
            if (isset($_FILES['kalolsavamCSV']['name']) && !empty($_FILES['kalolsavamCSV']['name'])) {
                $file_name = $this->General_Model->upload_kalolsavam_csv_data('kalolsavamCSV', 'kalolsavamCSV' . substr(fncUuid(), 10));
                if (!$file_name) {
                    $this->template->write('error', 'Invalid CSV file. Only .csv text files up to 8 MB are accepted.');
                    $this->template->write_view('content', 'import/import_csv_data', $this->Contents);
                    $this->template->load();
                    return;
                }
                if ($this->Import_Model->insert_import_data($file_name)) {
                    $this->template->write('message', 'CSV Data saved successfully');
                } else {
                    $this->template->write('error', 'Failed to Save CSV Data');
                }
            }

            $this->template->write_view('content', 'import/import_csv_data', $this->Contents);
            $this->template->load();
        } else
            redirect('welcome');
    }

    function backup_data() {

        // Load the DB utility class
        $this->load->dbutil();

        // Backup your entire database and assign it to a variable
        $backup = & $this->dbutil->backup();

        // Load the file helper and write the file to your server
        $this->load->helper('file');
     //   write_file($_SERVER['DOCUMENT_ROOT'] . '/kalolsavam_subdistrict_2022/dbBackup/kalolsavam_subdistrict2022_' . date('d-m-Y-h-i-s') . '.gz', $backup);
       
        $baseValue = basename(base_url());      
         
        $year = date('Y');

        write_file($_SERVER['DOCUMENT_ROOT'] . '/'.$baseValue.'/ dbBackup/kalolsavam_subdistrict'.$year.'_' . date('d-m-Y-h-i-s') . '.gz', $backup);
        // Load the download helper and send the file to your desktop
        $this->load->helper('download');
        force_download('kalolsavam_subdistrict'.$year.'_' . date('d-m-Y-h-i-s') . '.gz', $backup);

        $this->template->write_view('menu', 'menu', '');
        $this->template->load();
    }

    function backup_data_sql() {
        if ($this->session->get('USER_GROUP') != 'A' && $this->session->get('USER_GROUP') != 'W') {
            redirect('welcome');
            return;
        }

        $baseValue = basename(base_url());
        $backupDir = $_SERVER['DOCUMENT_ROOT'] . '/' . $baseValue . '/dbBackup/';

        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0777, true);
        }

        if ($this->request->getGet('do') === 'backup') {
            $sqlContent = $this->Import_Model->backup_tables();

            if ($sqlContent !== false && $sqlContent !== '') {
                $this->load->helper('download');
                $filename = 'kalolsavam_subdistrict_' . date('d-m-Y-h-i-s') . '.sql';
                force_download($filename, $sqlContent);
            } else {
                $message = 'Database backup failure. Please check it.';
                $this->template->write('error', $message);
            }
        } else {
            $this->template->write('message', 'Backup your database as a .sql file.');
        }

        $backupFiles = glob($backupDir . '*.sql');
        $backupFileNames = [];
        if ($backupFiles) {
            foreach ($backupFiles as $file) {
                $backupFileNames[] = basename($file);
            }
        }

        $this->Contents['backupFileNames'] = $backupFileNames;
        $this->Contents['backupDir'] = $backupDir;
        $this->template->write_view('content', 'import/backup_data_sql', $this->Contents);
        $this->template->write_view('menu', 'menu', '');
        $this->template->load();
    }

    /**
     * Write an audit log entry for a database restore attempt using the
     * project's existing table-based activity log convention (z_*_log).
     * Never logs passwords, credentials or file contents.
     *
     * @param string $action  e.g. 'restore'
     * @param string $status  'S' success, 'F' failure, 'D' denied
     * @param string $message Short human-readable result detail.
     * @return void
     */
    private function writeRestoreLog($action, $status, $message = '') {
        $uid  = (int)$this->session->get('USERID');
        $uname = (string)$this->session->get('USER_NAME');
        if ($uname === '' ) {
            $uname = (string)$this->session->get('USER');
        }
        $logdata = array(
            'user_id'            => $uid ?: 0,
            'user_name'          => $uname,
            'rev_district_code'  => (int)$this->session->get('DISTRICT') ?: 0,
            'sub_district_code'  => (int)$this->session->get('SUB_DISTRICT') ?: 0,
            'school_code'        => (int)$this->session->get('SCHOOL_CODE') ?: 0,
            'ip'                 => service('request')->getIPAddress(),
            'action'             => (string)$action,
            'status'             => (string)$status,
            'message'            => mb_substr($message, 0, 255),
        );
        try {
            $this->db->insert('z_restore_log', $logdata);
        } catch (\Throwable $e) {
            // Failures to write the audit log must not break the request.
        }
    }

    function restore_database() {

        $this->template->write_view('menu', 'menu', '');
        if ($this->session->get('USER_GROUP') != 'W') {
            $this->writeRestoreLog('restore', 'D', 'restore denied to non-Web-admin user');
            redirect('welcome');
            return;
        }

        // Generate a one-time server-side confirmation token when the form is shown.
        if (!$this->request->getPost()) {
            $confirmToken = bin2hex(random_bytes(16));
            $this->session->set('restore_confirm_token', $confirmToken);
            $this->Contents['confirm_token'] = $confirmToken;
            $this->template->write_view('content', 'import/restore_data', $this->Contents);
            $this->template->load();
            return;
        }

        // POST: enforce the confirmation server-side. A bare/direct POST to this
        // URL (without going through the confirmed form) is rejected.
        $sentToken = (string)$this->request->getPost('confirm_token');
        $sessionToken = (string)$this->session->get('restore_confirm_token');
        $typedConfirm = strtoupper(trim((string)$this->request->getPost('confirm_restore')));

        $authorized = ($sentToken !== ''
            && $sessionToken !== ''
            && hash_equals($sessionToken, $sentToken)
            && $typedConfirm === 'RESTORE');

        if (!$authorized) {
            $this->writeRestoreLog('restore', 'F', 'restore rejected: confirmation token missing or invalid');
            $this->template->write('error', 'Please confirm the restore by ticking the confirmation box on the form and submitting from there.');
            $this->Contents['confirm_token'] = $sessionToken;
            $this->template->write_view('content', 'import/restore_data', $this->Contents);
            $this->template->load();
            return;
        }

        if (isset($_FILES['kalolsavamdatabase']['name']) && !empty($_FILES['kalolsavamdatabase']['name'])) {
            $file_name = $this->General_Model->restore_database('kalolsavamdatabase', 'kalolsavamdatabase' . substr(fncUuid(), 10));
            if (!$file_name) {
                $this->writeRestoreLog('restore', 'F', 'restore rejected: invalid upload (type/size/content)');
                $this->template->write('error', 'Invalid database backup file. Only .sql text files up to 16 MB are accepted.');
                $this->template->write_view('content', 'import/restore_data', $this->Contents);
                $this->template->load();
                return;
            }

            $import_result = $this->Import_Model->restore_Database($file_name);

            if (TRUE == $import_result) {
                $this->writeRestoreLog('restore', 'S', 'database restored successfully');
                $this->template->write_view('menu', 'menu', '');
                $this->template->write('message', 'Restore Database successfully');
            } else {
                $this->writeRestoreLog('restore', 'F', 'database restore failed');
                $this->template->write('error', 'Failed to Restore Database');
            }
        } else {
            $this->writeRestoreLog('restore', 'F', 'restore rejected: no database file uploaded');
            $this->template->write('error', 'Please select a database backup (.sql) file to restore.');
        }

        $this->Contents['confirm_token'] = $this->session->get('restore_confirm_token');
        $this->template->write_view('content', 'import/restore_data', $this->Contents);
        $this->template->load();
    }

    function initialize_data() {

        $this->template->write_view('content', 'import/initialize_tables');
        $this->template->write_view('menu', 'menu', '');
        $this->template->load();
    }

    function initialize_database_confirm() {
        $initialize_data = $this->Import_Model->initialize_tables();
        $message = 'Database Initailzed sucessfully You can import the sudistrict data now';
        $this->template->write('message', $message);
        $this->import_district_kalolsavam_data();
    }

}
?>
