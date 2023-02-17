<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ProjectPage;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('project_pages')->truncate();

        ProjectPage::create([ 
            'id' => 1, 
            'parent_menu' => 0, 
            'label' => 'Dashboard', 
            'route_url' => 'admin.dashboard', 
            'is_display_in_menu' => 0, 
            'inner_routes' => 'admin.dashboard',
            'icon_class' => 'fa fa-dashboard', 
            'sr_no' => 1
        ]);

        ProjectPage::create([
            'id' => 2,
            'parent_menu' => 0,
            'label' => 'Users',
            'route_url' => null,
            'icon_class' => 'fa fa-users',
            'is_display_in_menu' => 1,
            'sr_no' => 2
        ]);

        ProjectPage::create([
            'id' => 3,
            'parent_menu' => 2,
            'label' => 'User List',
            'route_url' => 'admin.users.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.users.list,admin.users.addorupdate,admin.alluserslist,admin.users.changeuserstatus,admin.users.edit,admin.users.delete,admin.users.permission,admin.users.savepermission'
        ]);

        ProjectPage::create([
            'id' => 4,
            'parent_menu' => 0,
            'label' => 'Match',
            'icon_class' => 'fa fa-futbol-o',
            'route_url' => 'admin.match.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.match.list,admin.match.addorupdate,admin.allmatchlist,admin.match.changematchstatus,admin.match.edit,admin.match.delete',
            'sr_no' => 7
        ]);

        ProjectPage::create([
            'id' => 5,
            'parent_menu' => 0,
            'label' => 'Country',
            'route_url' => 'admin.country.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.country.list,admin.country.addorupdate,admin.allcountryslist,admin.country.changecountrystatus,admin.country.edit,admin.country.delete,admin.country.permission,admin.country.savepermission',
            'icon_class' => 'fa fa-flag',
            'sr_no' => 3
        ]);

        ProjectPage::create([
            'id' => 6,
            'parent_menu' => 0,
            'label' => 'Tournament',
            'route_url' => 'admin.tournament.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.tournament.list,admin.tournament.addorupdate,admin.alltournamentslist,admin.tournament.changetournamentstatus,admin.tournament.edit,admin.tournament.delete,admin.tournament.permission,admin.tournament.savepermission',
            'icon_class' => 'fa fa-trophy',
            'sr_no' => 4
        ]);

        ProjectPage::create([
            'id' => 7,
            'parent_menu' => 0,
            'label' => 'Stadium',
            'route_url' => 'admin.stadium.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.stadium.list,admin.stadium.addorupdate,admin.allstadiumslist,admin.stadium.changestadiumstatus,admin.stadium.edit,admin.stadium.delete,admin.stadium.permission,admin.stadium.savepermission',
            'icon_class' => 'fa fa-users',
            'sr_no' => 5
        ]);

        ProjectPage::create([
            'id' => 8,
            'parent_menu' => 0,
            'label' => 'Series',
            'route_url' => 'admin.series.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.series.list,admin.series.addorupdate,admin.allseriesslist,admin.series.changeseriesstatus,admin.series.edit,admin.series.delete,admin.series.permission,admin.series.savepermission',
            'icon_class' => 'fa fa-user',
            'sr_no' => 6
        ]);

        ProjectPage::create([
            'id' => 9,
            'parent_menu' => 0,
            'label' => 'Settings',
            'route_url' => 'admin.settings.list',
            'icon_class' => 'fa fa-cog',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.settings.list,admin.settings.edit',
            'sr_no' => 10
        ]);

        
        $users = User::where('role',"!=",1)->get();
        $project_page_ids1 = ProjectPage::where('parent_menu',0)->where('is_display_in_menu',0)->pluck('id')->toArray();
        $project_page_ids2 = ProjectPage::where('parent_menu',"!=",0)->where('is_display_in_menu',1)->pluck('id')->toArray();
        $project_page_ids = array_merge($project_page_ids1,$project_page_ids2);
        foreach ($users as $user){
            foreach ($project_page_ids as $pid){
                $user_permission = UserPermission::where('user_id',$user->id)->where('project_page_id',$pid)->first();
                if (!$user_permission){
                    $userpermission = new UserPermission();
                    $userpermission->user_id = $user->id;
                    $userpermission->project_page_id = $pid;
                    $userpermission->save();
                }
            }
        }

    }
}
