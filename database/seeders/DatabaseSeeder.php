<?php

namespace Database\Seeders;

use App\Models\AllUser;
use App\Models\Disability;
use App\Models\Valid;
use App\Models\EducationLevel;
use App\Models\Skill;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Socials;
use App\Models\UserInfo;
use App\Models\TrainingProgram;
use App\Models\WorkSetup;
use App\Models\WorkType;
use App\Models\ProgramCriteria;
use App\Models\JobCriteria;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //ROLES
        $admin = Role::create(['role_name' => 'Admin']);
        $pwd = Role::create(['role_name' => 'PWD']);
        $trainer = Role::create(['role_name' => 'Training Agency']);
        $employer = Role::create(['role_name' => 'Employer']);
        $sponsor = Role::create(['role_name' => 'Sponsor']);

        //DISABILITIES
        $none = Disability::create(['disability_name' => 'Not Applicable']);
        $onearm = Disability::create(['disability_name' => 'One Arm Amputee']);
        $botharm = Disability::create(['disability_name' => "Bilateral Amputee (Both Arms)"]);
        $oneleg = Disability::create(['disability_name' => 'One Leg Amputee']);
        $bothleg = Disability::create(['disability_name' => "Bilateral Amputee (Both Legs)"]);
        $hear = Disability::create(['disability_name' => 'Hearing Impaired']);
        $speech = Disability::create(['disability_name' => 'Speech Impairment']);
        $visual = Disability::create(['disability_name' => 'Visually Impaired']);

        // EDUCATION LEVELS
        $not_applicable = EducationLevel::create(['education_name' => 'Not Applicable']);
        $hsgrad = EducationLevel::create(['education_name' => 'High School Graduate']);
        $somecoll = EducationLevel::create(['education_name' => 'Some College']);
        $vocational = EducationLevel::create(['education_name' => 'Vocational']);
        $bachdegree = EducationLevel::create(['education_name' => "Bachelor's Degree"]);

        // SKILLS
        $programming = Skill::create(['title' => 'Programming']);
        $communication = Skill::create(['title' => 'Communication']);
        $graphic = Skill::create(['title' => 'Graphic Design']);
        $data_analysis = Skill::create(['title' => 'Data Analysis']);
        $carpentry = Skill::create(['title' => 'Carpentry']);

        // WORK SETUPS
        $onsite = WorkSetup::create(['name' => 'Onsite']);
        $remote = WorkSetup::create(['name' => 'Remote']);
        $hybrid = WorkSetup::create(['name' => 'Hybrid']);

        // WORK TYPE
        $fulltime = WorkType::create(['name' => 'Full-time']);
        $parttime = WorkType::create(['name' => 'Part-time']);

        // SOCIALS
        $fb = Socials::create(['name' => 'Facebook']);
        $ig = Socials::create(['name' => 'Instagram']);
        $git = Socials::create(['name' => 'Github']);
        $web = Socials::create(['name' => 'Website']);
        // $fb = Socials::create(['name' => 'Facebook']);

        // Valid ID Numbers
        $id_one = Valid::create(['valid_id_number' => '13-5416-000-0000001']);
        $id_two = Valid::create(['valid_id_number' => '13-5416-000-0000002']);
        $id_three = Valid::create(['valid_id_number' => '13-5416-000-0000003']);
        $id_four = Valid::create(['valid_id_number' => '13-5416-000-0000004']);
        $id_five = Valid::create(['valid_id_number' => '13-5416-000-0000005']);
        $id_six = Valid::create(['valid_id_number' => '13-5416-000-0000006']);
        $id_seven = Valid::create(['valid_id_number' => '13-5416-000-0000007']);
        $id_eight = Valid::create(['valid_id_number' => '13-5416-000-0000008']);
        $id_nine = Valid::create(['valid_id_number' => '13-5416-000-0000009']);
        $id_ten = Valid::create(['valid_id_number' => '13-5416-000-0000010']);
        $id_eleven = Valid::create(['valid_id_number' => '13-5416-000-0000011']);
        $id_twelve = Valid::create(['valid_id_number' => '13-5416-000-0000012']);
        $id_thirteen = Valid::create(['valid_id_number' => '13-5416-000-0000013']);
        $id_fourteen = Valid::create(['valid_id_number' => '13-5416-000-0000014']);
        $id_fifteen = Valid::create(['valid_id_number' => '13-5416-000-0000015']);
        $id_sixteen = Valid::create(['valid_id_number' => '13-5416-000-0000016']);
        $id_seventeen = Valid::create(['valid_id_number' => '13-5416-000-0000017']);
        $id_eighteen = Valid::create(['valid_id_number' => '13-5416-000-0000018']);
        $id_nineteen = Valid::create(['valid_id_number' => '13-5416-000-0000019']);
        $id_twenty = Valid::create(['valid_id_number' => '13-5416-000-0000020']);

        $location = ProgramCriteria::create(['name' => 'Location', 'weight' => '30']);
        $age = ProgramCriteria::create(['name' => 'Age', 'weight' => '20']);
        $educ = ProgramCriteria::create(['name' => 'Educational Background', 'weight' => '25']);
        $skills = ProgramCriteria::create(['name' => 'Skills', 'weight' => '20']);
        $rating = ProgramCriteria::create(['name' => 'Rating', 'weight' => '5']);

        $location = JobCriteria::create(['name' => 'Location', 'weight' => '30']);
        $skills = JobCriteria::create(['name' => 'Skills', 'weight' => '10']);
        $certifiedSkills = JobCriteria::create(['name' => 'Certified Skills', 'weight' => '60']);

        $adminuser = User::create([
            'email' => 'kler@example.com',
            'password' => Hash::make('qwe1234'),

        ]);

        UserInfo::create([
            'name' => 'Evryl Claire',
            'contactnumber' => '09123456789',
            'latitude' => 10.30489026928145,
            'longitude' => 123.74810749843749,
            'disability_id' => $none->id,
            'educational_id' => $not_applicable->id,
            'user_id' => $adminuser->id,
            'registration_status' => 'Activated'
        ]);


        $adminuser->role()->attach($admin);

        // $pwduser = User::create([
        //     'email' => 'pwd@example.com',
        //     'password' => Hash::make('qwe1234'),

        // ]);

        // UserInfo::create([
        //     'name' => 'Juan Dela Cruz',
        //     'contactnumber' => '09123456789',
        //     'state' => 'Cebu',
        //     'city' => 'City Of Talisay',
        //     'disability_id' => $leftarm->id,
        //     'educational_id' => $bachdegree->id,
        //     'user_id' => $pwduser->id,
        // ]);


        // $pwduser->role()->attach($pwd);

        // $traineruser1 = User::create([
        //     'email' => 'trainer@example.com',
        //     'password' => Hash::make('sheesh'),

        // ]);

        // UserInfo::create([
        //     'name' => 'BrightFuture Training',
        //     'contactnumber' => '09123456789',
        //     'city' => 'City Of Cebu',
        //     'state' => 'Cebu',
        //     'disability_id' => $none->id,
        //     'educational_id' => $not_applicable->id,
        //     'user_id' => $traineruser1->id,
        // ]);

        // TrainingProgram::create([
        //     'id' => '001',
        //     'agency_id' => $traineruser1->id,
        //     'title' => 'EmpowerTech Skills Development Program',
        //     'description' => 'The EmpowerTech Skills Development Program is a comprehensive training initiative aimed at enhancing the technical and vocational skills of people with disabilities. The program focuses on providing hands-on experience and practical knowledge to enable participants to thrive in the digital economy.',
        //     'state' => 'Cebu',
        //     'city' => 'City Of Cebu',
        //     'participants' => 30,
        //     'start' => date("Y-m-d"),
        //     'end' => date("Y-m-d"),
        //     'disability_id' => $bothleg->id,
        //     'education_id' => $hsgrad->id,
        //     'skill_id' => $programming->id,
        // ]);

        // $traineruser1->role()->attach($trainer);

        // $traineruser2 = User::create([
        //     'email' => 'trainer2@example.com',
        //     'password' => Hash::make('sheesh'),

        // ]);

        // UserInfo::create([
        //     'name' => 'Aspire Training Solutions',
        //     'contactnumber' => '09123456789',
        //     'city' => 'City Of Talisay',
        //     'state' => 'Cebu',
        //     'disability_id' => $none->id,
        //     'educational_id' => $not_applicable->id,
        //     'user_id' => $traineruser2->id,
        // ]);

        // TrainingProgram::create([
        //     'id' => '002',
        //     'agency_id' => $traineruser2->id,
        //     'title' => 'InclusiveTech Career Advancement Program',
        //     'description' => 'The InclusiveTech Career Advancement Program is designed to equip people with disabilities with advanced skills in technology and professional development. This program focuses on bridging the skills gap and providing participants with the knowledge and confidence to pursue high-demand careers in the tech industry.',
        //     'state' => 'Cebu',
        //     'city' => 'City Of Talisay',
        //     'participants' => 30,
        //     'start' => date("Y-m-d"),
        //     'end' => date("Y-m-d"),
        //     'disability_id' => $hear->id,
        //     'education_id' => $somecoll->id,
        //     'skill_id' => $carpentry->id,
        // ]);

        // $traineruser2->role()->attach($trainer);
    }
}
