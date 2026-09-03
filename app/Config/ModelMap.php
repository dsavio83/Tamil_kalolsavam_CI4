<?php

namespace Config;

/**
 * Model Map
 *
 * Maps CI3-style model identifiers to their CodeIgniter 4 fully-qualified
 * class names. Used by the compatibility layer (BaseController / CiLoader)
 * to instantiate models just like CI3's $this->load->model().
 */
class ModelMap
{
    /**
     * Keyed by CI3 load path (lowercased), e.g. 'report/prereport_model'.
     * Value is the FQCN.
     *
     * @var array<string, string>
     */
    public static array $byPath = [
        'export_model'                          => \App\Models\Export_Model::class,
        'general_model'                         => \App\Models\General_Model::class,
        'import_model'                          => \App\Models\Import_Model::class,
        'itemreport_model'                      => \App\Models\Itemreport_Model::class,
        'item/item_master_model'                => \App\Models\Item\Item_Master_Model::class,
        'registration_model'                    => \App\Models\Registration_Model::class,
        'registration_model1'                   => \App\Models\Registration_Model1::class,
        'session_model'                         => \App\Models\Session_Model::class,
        'admin/kalolsavam_model'                => \App\Models\Admin\Kalolsavam_Model::class,
        'certificate/certificate_model'         => \App\Models\Certificate\Certificate_Model::class,
        'login/cluster_statistics_model'        => \App\Models\Login\Cluster_Statistics_Model::class,
        'login/login_model'                     => \App\Models\Login\Login_Model::class,
        'pictures/photos_model'                 => \App\Models\Pictures\Photos_Model::class,
        'publishresult/resultindex_model'       => \App\Models\Publishresult\Resultindex_Model::class,
        'report/afterresult_model'              => \App\Models\Report\Afterresult_model::class,
        'report/masterreport_model'             => \App\Models\Report\Masterreport_Model::class,
        'report/prereport_model'                => \App\Models\Report\Prereport_Model::class,
        'report/resultindex_model'              => \App\Models\Report\Resultindex_Model::class,
        'report/timefest_model'                 => \App\Models\Report\Timefest_model::class,
        'result/result_model'                   => \App\Models\Result\Result_Model::class,
        'schools/registration_model'            => \App\Models\Schools\Registration_Model::class,
        'schools/registration_modelold'         => \App\Models\Schools\Registration_modelold::class,
        'schools/registration_report_model'     => \App\Models\Schools\Registration_Report_Model::class,
        'schools/school_master_model'           => \App\Models\Schools\School_Master_Model::class,
        'schools1/registration_model'           => \App\Models\Schools1\Registration_Model::class,
        'schools1/registration_modelold'        => \App\Models\Schools1\Registration_modelold::class,
        'schools1/registration_report_model'    => \App\Models\Schools1\Registration_Report_Model::class,
        'schools1/school_master_model'          => \App\Models\Schools1\School_Master_Model::class,
        'stages/allotment_model'                => \App\Models\Stages\Allotment_Model::class,
        'stages/allotmentfest_model'            => \App\Models\Stages\Allotmentfest_Model::class,
        'stages/festival_scheduling_model'      => \App\Models\Stages\Festival_Scheduling_Model::class,
        'stages/participant_model'              => \App\Models\Stages\Participant_Model::class,
        'stages/stage_model'                    => \App\Models\Stages\Stage_Model::class,
        'staticreport/itemreports_model'        => \App\Models\Staticreport\Itemreports_Model::class,
        'staticreport/participants_with_more_items_model' => \App\Models\Staticreport\Participants_with_more_items_model::class,
        'staticreport/stage_report_model'       => \App\Models\Staticreport\Stage_Report_Model::class,
        'staticreport/tabulation_model'         => \App\Models\Staticreport\Tabulation_Model::class,
        'user/admin_users_model'                => \App\Models\User\Admin_Users_Model::class,
        'user/admin_users_model1'               => \App\Models\User\admin_users_model1::class,
        'user/user_cluster_model'               => \App\Models\User\User_Cluster_Model::class,
        'user/user_registration_model'          => \App\Models\User\User_Registration_Model::class,
    ];

    /**
     * Keyed by basename (lowercased) -> FQCN for lazy resolution.
     *
     * @var array<string, string>
     */
    public static array $byName = [];

    public static function init(): void
    {
        if (self::$byName !== []) {
            return;
        }
        foreach (self::$byPath as $path => $fqcn) {
            $base = strtolower(basename(str_replace('\\', '/', $path)));
            if (!isset(self::$byName[$base])) {
                self::$byName[$base] = $fqcn;
            }
        }
    }
}
