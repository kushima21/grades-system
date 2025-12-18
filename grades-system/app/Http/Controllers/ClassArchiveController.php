<?php

namespace App\Http\Controllers;

use App\Models\ClassArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth
use App\Models\ArchivedFinalGrade;
use App\Models\Classes;
use App\Models\User;
use TCPDF;


class CustomPDF extends TCPDF
{
    public function Header()
    {
        // Leave empty to remove the default header (including the black line)
    }
}


class ClassArchiveController extends Controller
{
    public function index(Request $request)
{
    $termOrder = ['Prelim', 'Midterm', 'Semi-Finals', 'Finals'];

    $loggedInUser = Auth::user();
    $loggedInInstructor = $loggedInUser->name;
    $roles = explode(',', $loggedInUser->role);

    $isAdmin = in_array('admin', $roles);

    $query = ClassArchive::query();

    if (!$isAdmin) {
        $query->where('instructor', $loggedInInstructor);
    }

    if ($request->filled('academic_year')) {
        $query->where('academic_year', $request->academic_year);
    }

    if ($request->filled('course_no')) {
        $query->where('course_no', 'LIKE', '%' . $request->course_no . '%');
    }

    $records = $query->orderBy('academic_year', 'desc')
        ->orderBy('academic_period')
        ->orderBy('descriptive_title')
        ->orderBy('course_no')
        ->get();

    $uniqueInstructors = ClassArchive::selectRaw('DISTINCT TRIM(LOWER(instructor)) as instructor')
        ->orderBy('instructor')
        ->pluck('instructor')
        ->map(fn($name) => ucwords($name))
        ->unique()
        ->values();

    // ✅ Group by classID and preserve program
    $archivedData = $records->groupBy('academic_year')
        ->map(function ($yearGroup) use ($termOrder) {
            return $yearGroup->groupBy('academic_period')
                ->map(function ($periodGroup) use ($termOrder) {
                    return $periodGroup->groupBy('classID')
                        ->map(function ($classGroup) use ($termOrder) {
                            return $classGroup->groupBy('instructor')
                                ->map(function ($instructorGroup) use ($termOrder) {
                                    return $instructorGroup->groupBy('descriptive_title')
                                        ->map(function ($titleGroup) use ($termOrder) {
                                            return $titleGroup->groupBy('periodic_term')
                                                ->sortBy(fn($_, $key) => array_search($key, $termOrder));
                                        });
                                });
                        });
                });
        });

    // ✅ Include program in finalGrades
    $finalGrades = ArchivedFinalGrade::all()->groupBy(function ($item) {
        return $item->academic_year . '|' . $item->academic_period . '|' . $item->classID . '|' . $item->instructor . '|' . $item->descriptive_title . '|' . $item->studentID;
    });

    return view('instructor.my_class_archive', compact('archivedData', 'uniqueInstructors', 'finalGrades'));
}








    public function generateGradeSheetPDF(Request $request)
{
    $academic_year     = $request->academic_year;
    $academic_period   = $request->academic_period;
    $course_no         = $request->course_no;
    $instructor        = $request->instructor;
    $descriptive_title = $request->descriptive_title;
    $classID           = $request->classID;

    // ================= FETCH FINAL GRADES =================
    $finalGrades = \App\Models\ArchivedFinalGrade::where([
        ['academic_year', $academic_year],
        ['academic_period', $academic_period],
        ['classID', $classID],
        ['course_no', $course_no],
        ['instructor', $instructor],
        ['descriptive_title', $descriptive_title],
    ])
    ->orderBy('department')
    ->orderBy('abbreviation')
    ->orderBy('name')
    ->get();

    if ($finalGrades->isEmpty()) {
        return back()->with('error', 'No records found.');
    }

    // ================= GROUP BY DEPARTMENT + ABBREVIATION =================
    $gradesByGroup = $finalGrades->groupBy(function ($item) {
        return trim($item->department) . '|' . trim($item->abbreviation);
    });

    // ================= MAPPINGS =================
    $programHeads = [
        'Computer Science'            => 'Marjon D. Senarlo, MSIT',
        'Business Administration'     => 'Arlene N. Bacus, MBA',
        'Education'                   => 'Everose C. Toylo, M.Ed.',
        'Criminology'                 => 'Jennilyn B. Obena, MSCrim',
        'English Language Studies'    => 'Anacleto S. Dolar Jr., MATE',
        'Social Work'                 => 'Sherlita A. Sintos, RSW',
    ];

    $deptLogos = [
        'COLLEGE OF COMPUTER SCIENCE'          => public_path('system_images/comsci.jpg'),
        'COLLEGE OF BUSINESS ADMINISTRATION'   => public_path('system_images/cba.jpg'),
        'COLLEGE OF EDUCATION'                 => public_path('system_images/educ.jpg'),
        'COLLEGE OF CRIMINOLOGY'               => public_path('system_images/crim.jpg'),
        'COLLEGE OF ENGLISH LANGUAGE STUDIES'  => public_path('system_images/baels.jpg'),
        'COLLEGE OF SOCIAL WORK'               => public_path('system_images/sw.jpg'),
    ];

    $schoolLogo = public_path('system_images/logo.jpg');

    // ================= CREATE PDF =================
    $pdf = new CustomPDF('P', 'mm', [215.9, 355.6], true, 'UTF-8', false);
    $pdf->SetCreator('CKCM Grading System');
    $pdf->SetAuthor($instructor);
    $pdf->SetTitle('Grading Sheet');
    $pdf->SetMargins(10, 10, 10, true);

    // ================= LOOP PER GROUP =================
    foreach ($gradesByGroup as $groupKey => $students) {

        [$department, $abbreviation] = explode('|', $groupKey);
        $schedule = $students->first()->schedule ?? '';

        // ================= FORCE COLLEGE BY DEPARTMENT =================
  // ================= FORCE COLLEGE BY ABBREVIATION =================
$educationAbbreviations = [
    'BEED',
    'BSED',
    'BSED - MATH',
    'BSED - ENGLISH',
    'BSED-MATH',
    'BSED-ENGLISH'
];

$bsbaAbbreviations = [
    'BSBA - FM',
    'BSBA -FM',
    'BSBA-OM',
    'BSBA - OM',
    'BSBA'
];

$bscsAbbreviations = [
    'BSCS',
    'BSCS - A',
    'BSCS - B',
    'BSCS-A',
    'BSCS-B'
];

$abbrUpper = strtoupper(trim($abbreviation));

if (in_array($abbrUpper, $educationAbbreviations)) {
    // ================= FORCE EDUCATION =================
    $college    = 'COLLEGE OF EDUCATION';
    $approvedBy = $programHeads['Education'];
    $deptLogo   = $deptLogos['COLLEGE OF EDUCATION'];

} elseif (in_array($abbrUpper, $bsbaAbbreviations)) {
    // ================= FORCE BUSINESS ADMINISTRATION =================
    $college    = 'COLLEGE OF BUSINESS ADMINISTRATION';
    $approvedBy = $programHeads['Business Administration'];
    $deptLogo   = $deptLogos['COLLEGE OF BUSINESS ADMINISTRATION'];

} elseif (in_array($abbrUpper, $bscsAbbreviations)) {
    // ================= FORCE COMPUTER SCIENCE =================
    $college    = 'COLLEGE OF COMPUTER SCIENCE';
    $approvedBy = $programHeads['Computer Science'];
    $deptLogo   = $deptLogos['COLLEGE OF COMPUTER SCIENCE'];

} elseif ($department === 'Education') {
    // ================= FORCE EDUCATION BY DEPARTMENT =================
    $college    = 'COLLEGE OF EDUCATION';
    $approvedBy = $programHeads['Education'];
    $deptLogo   = $deptLogos['COLLEGE OF EDUCATION'];

} elseif ($department === 'Business Administration') {
    // ================= FORCE BUSINESS ADMINISTRATION BY DEPARTMENT =================
    $college    = 'COLLEGE OF BUSINESS ADMINISTRATION';
    $approvedBy = $programHeads['Business Administration'];
    $deptLogo   = $deptLogos['COLLEGE OF BUSINESS ADMINISTRATION'];

} else {
    // ================= DEFAULT =================
    $college    = 'COLLEGE OF ' . strtoupper($department);
    $approvedBy = $programHeads[$department] ?? '___________________________';
    $deptLogo   = $deptLogos[$college] ?? $schoolLogo;
}


    $pdf->AddPage();

        // ------------------- HTML Layout (UNCHANGED) -------------------
        $html = '
       <table width="100%">
        <tr>
            <td width="20%" align="right">
                <img src="' . $schoolLogo . '" width="70" >
            </td>
            <td width="60%" align="center">
                <p style="font-size:12px; font-weight:bold; line-height:2px;">CHRIST THE KING COLLEGE DE MARANDING, INC.</p>
                <p style="font-size:10px; line-height:1px;">Maranding Lala, Lanao del Norte</p>
                <p style="font-size:11px; font-weight:bold; line-height:15px;">' . $college . '</p>
                <p style="font-size:10px; font-weight:bold; line-height:10px;">GRADING SHEET</p>
                <p style=" line-height:1px;"></p>
            </td>
            <td width="20%" align="left">
                <img src="' . $deptLogo . '" width="70">
            </td>
        </tr>
         </table>
         <br>
        <table cellpadding="1" style=" margin-left:10px; font-size:10px;">
            <tr>
                <td><b>Instructor:</b> ' . $instructor . '</td>
                <td><b>Date:</b> ' . date('m/d/Y') . '</td>
            </tr>
            <tr>
                <td><b>Course Code:</b> '.$course_no.' ('.$abbreviation.')</td>
                <td><b>AY:</b> ' . $academic_year . '</td>
            </tr>
            <tr>
                <td><b>Descriptive Title:</b> ' . $descriptive_title . '</td>
                <td><b>Semester:</b> ' . $academic_period . '</td>
            </tr>
            <tr>
                <td><b>Number of Student:</b> ' . $students->count() . '</td>
                <td><b>Schedule:</b> ' . $schedule . '</td>
            </tr>
        </table>

        <table border="1" cellpadding="2" >
            <thead>
                <tr style="background-color:#eee; font-size:10px;" >
                    <th width="40%"  style="text-align:center;">Name of Student</th>
                    <th width="10%"  style="text-align:center;">Prelim</th>
                    <th width="10%"  style="text-align:center;">Midterm</th>
                    <th width="15%"  style="text-align:center;">Semi-Final</th>
                    <th width="10%"  style="text-align:center;">Final</th>
                    <th width="15%"  style="text-align:center;">Remarks</th>
                </tr>
            </thead>
            <tbody>
        ';
        foreach ($students as $student) {
            $html .= '
                <tr style="font-size:10px;">
                    <td width="40%"  style="text-align:start;">' . htmlspecialchars($student->name) . '</td>
                    <td width="10%"  style="text-align:center;">' . htmlspecialchars($student->prelim) . '</td>
                    <td width="10%"  style="text-align:center;">' . htmlspecialchars($student->midterm) . '</td>
                    <td width="15%"  style="text-align:center;">' . htmlspecialchars($student->semi_finals) . '</td>
                    <td width="10%"  style="text-align:center;">' . htmlspecialchars($student->final) . '</td>
                    <td width="15%"  style="text-align:center;">' . htmlspecialchars($student->remarks) . '</td>
                </tr>
            ';
        }
        $html .= '
            <tr>
                <td colspan="6" style="text-align:center; font-weight:bold; border:none; font-size: 9px;">*******Nothing Follows********</td>
            </tr>
            </tbody></table><br>

        <br>
       <table>
            <tr>
                <td colspan="2" style="font-size:10px; text-align:left;">
                    Submitted by: <b style="font-size:9px;">' . strtoupper($instructor) . '</b><br>
                    <table width="100%"><tr><td align="center" style="font-size:9px;">Instructor</td><td></td></tr></table>
                </td>
            </tr>
            <tr>
              <td colspan="2" style="font-size:10px; text-align:center;"></td>
            </tr>
            <tr>

           <td style="font-size:10px; ">
                Approved by: <b style="font-size:9px;">' . strtoupper($approvedBy) . '</b><br>
                 <table width="100%"><tr><td align="center" style="font-size:9px;">Dean</td></tr></table>
            </td>
            <td style="font-size:10px; ">
                Submitted to:<b style="font-size:9px;"> ' . strtoupper('ELVYN P. SALMERON, MMEM') . '</b> <br>
                 <table width="100%"><tr><td align="center" style="font-size:9px;">Registrar</td></tr></table>
            </td>
        </tr>
        </table>
        <br>
        <p style="font-size:9px; line-height:10px; text-align:center;">
            1.0=<b>EXCELLENT</b> &nbsp; 1.25-1.5=<b>VERY SATISFACTORY</b> &nbsp; 1.75-2.0=<b>SATISFACTORY</b>
            2.25-2.5=<b>FAIR</b> &nbsp; 2.75-3.0=<b>POOR</b> &nbsp; 5.0=<b>FAILED</b>
        </p>
        <div style="height: 10px;"></div>
        <p style="line-height:0; font-size:9px; ">*Attachment: Summary of Students with Deficiency</p>
        <p style="line-height:0; font-size:9px; ">*Copy Furnished: Instructor, College Dean and Registrar</p>
        ';

        $pdf->writeHTML($html, true, false, true, false, '');
    }

    $pdf->Output('gradesheet.pdf', 'I');
    exit;
}

}