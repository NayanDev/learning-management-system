<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AnswerParticipantController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AssesmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\CertificationExternalController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DirectorSignatureController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\JobdescSectionController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\MateriLogController;
use App\Http\Controllers\MatrikController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ReportTrainingController;
use App\Http\Controllers\ResultQuestionController;
use App\Http\Controllers\ResumeMateriController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\TemplateCertificationController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainingAnalystController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TrainingLocationController;
use App\Http\Controllers\TrainingNeedController;
use App\Http\Controllers\TrainingParticipantController;
use App\Http\Controllers\TrainingScheduleController;
use App\Http\Controllers\TrainingUnplanController;
use App\Http\Controllers\TrainingUnplanParticipantController;
use App\Http\Controllers\TrainingWorkshopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkshopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::post('/section/jobdesc/test', function (Request $request) {

    // return response()->json([
    //     'status' => true,
    //     'data' => [
    //         'name' => $request->input('name'),
    //         'section_id' => $request->input('section_id'),
    //         'file_exists' => $request->hasFile('file'),
    //     ]
    // ]);

// });

Route::get('/', [AuthController::class, 'login'])->name('login')->middleware('web');
// Route Signature Verified
Route::get('signature-verified-x', [UserController::class, 'signatureVerified'])->name('signature.verified');
Route::get('apitest', [TrainingParticipantController::class, 'getFilteredApiData']);

// Halaman development - jangan dihapus
Route::get('/matrik-view', function() {
    return view('pdf.matrik_karyawan');
})->name('matrik.view');

// Route::get('/silabus-pdf', function() {
//     $pdf = Pdf::loadView('pdf.silabus')->setPaper('a4', 'portrait');;
//     return $pdf->stream('silabus.pdf'); // <-- ini untuk tampil di browser
// })->name('silabus.view');


Route::get('/api-riwayat-pelatihan', [AttendanceController::class, 'TrainingHistoryApi'])->name('api.pegawai');

Route::get('trainer-signature', [TrainerController::class, 'signatureExternal'])->name('trainer.signature.external');
Route::post('trainer-signature', [TrainerController::class, 'storeSignature'])->name('trainer.signature.store');

Route::get('/evaluation-bulk-public', [EvaluationController::class, 'evaluationBulk'])->name('evaluation.bulk');
Route::post('/submit-evaluation-bulk', [EvaluationController::class, 'submitEvaluationBulk'])->name('submit.evaluation.bulk');


    Route::get('signature-verified/{model}/{id}/{user}', [ApprovalController::class, 'signatureVerified'])->name('signature.verified');
    Route::get('director-signature-verified/{model}/{id}', [DirectorSignatureController::class, 'signatureVerified'])->name('director.signature.verified');

Route::group(['middleware' => ['web', 'auth']], function () {
    // Route Dashboard
    Route::resource('dashboard', DashboardController::class);
    Route::get('dashboard-api', [DashboardController::class, 'indexApi'])->name('dashboard.listapi');
    Route::get('dashboard-export-pdf-default', [DashboardController::class, 'exportPdf'])->name('dashboard.export-pdf-default');
    Route::get('dashboard-export-excel-default', [DashboardController::class, 'exportExcel'])->name('dashboard.export-excel-default');
    Route::post('dashboard-import-excel-default', [DashboardController::class, 'importExcel'])->name('dashboard.import-excel-default');

    // Route Users
    Route::resource('user', UserController::class);
    Route::get('user-api', [UserController::class, 'indexApi'])->name('user.listapi');
    Route::get('user-export-pdf-default', [UserController::class, 'exportPdf'])->name('user.export-pdf-default');
    Route::get('user-export-excel-default', [UserController::class, 'exportExcel'])->name('user.export-excel-default');
    Route::post('user-import-excel-default', [UserController::class, 'importExcel'])->name('user.import-excel-default');

    // Route Training
    Route::resource('training', TrainingController::class);
    Route::get('training-api', [TrainingController::class, 'indexApi'])->name('training.listapi');
    Route::get('training-export-pdf-default', [TrainingController::class, 'exportPdf'])->name('training.export-pdf-default');
    Route::get('training-export-excel-default', [TrainingController::class, 'exportExcel'])->name('training.export-excel-default');
    Route::post('training-import-excel-default', [TrainingController::class, 'importExcel'])->name('training.import-excel-default');

    // Route Training Analyst
    Route::resource('training-analyst', TrainingAnalystController::class);
    Route::get('training-analyst-api', [TrainingAnalystController::class, 'indexApi'])->name('training-analyst.listapi');
    Route::get('training-analyst-export-pdf-default', [TrainingAnalystController::class, 'exportPdf'])->name('training-analyst.export-pdf-default');
    Route::get('training-analyst-export-excel-default', [TrainingAnalystController::class, 'exportExcel'])->name('training-analyst.export-excel-default');
    Route::post('training-analyst-import-excel-default', [TrainingAnalystController::class, 'importExcel'])->name('training-analyst.import-excel-default');

    // Route Training Need
    Route::resource('training-need', TrainingNeedController::class);
    Route::get('training-need-api', [TrainingNeedController::class, 'indexApi'])->name('training-need.listapi');
    Route::get('training-need-export-pdf-default', [TrainingNeedController::class, 'exportPdf'])->name('training-need.export-pdf-default');
    Route::get('training-need-export-excel-default', [TrainingNeedController::class, 'exportExcel'])->name('training-need.export-excel-default');
    Route::post('training-need-import-excel-default', [TrainingNeedController::class, 'importExcel'])->name('training-need.import-excel-default');

    // Route Training Workshop
    Route::resource('training-workshop', TrainingWorkshopController::class);
    Route::get('training-workshop-api', [TrainingWorkshopController::class, 'indexApi'])->name('training-workshop.listapi');
    Route::get('training-workshop-export-pdf-default', [TrainingWorkshopController::class, 'exportPdf'])->name('training-workshop.export-pdf-default');
    Route::get('training-workshop-export-excel-default', [TrainingWorkshopController::class, 'exportExcel'])->name('training-workshop.export-excel-default');
    Route::post('training-workshop-import-excel-default', [TrainingWorkshopController::class, 'importExcel'])->name('training-workshop.import-excel-default');

    // Route Training Participant
    Route::resource('training-participant', TrainingParticipantController::class);
    Route::get('training-participant-api', [TrainingParticipantController::class, 'indexApi'])->name('training-participant.listapi');
    Route::get('training-participant-export-pdf-default', [TrainingParticipantController::class, 'exportPdf'])->name('training-participant.export-pdf-default');
    Route::get('training-participant-export-excel-default', [TrainingParticipantController::class, 'exportExcel'])->name('training-participant.export-excel-default');
    Route::post('training-participant-import-excel-default', [TrainingParticipantController::class, 'importExcel'])->name('training-participant.import-excel-default');

    // Route Training Schedule
    Route::resource('training-schedule', TrainingScheduleController::class);
    Route::get('training-schedule-api', [TrainingScheduleController::class, 'indexApi'])->name('training-schedule.listapi');
    Route::get('training-schedule-export-pdf-default', [TrainingScheduleController::class, 'exportPdf'])->name('training-schedule.export-pdf-default');
    Route::get('training-schedule-export-excel-default', [TrainingScheduleController::class, 'exportExcel'])->name('training-schedule.export-excel-default');
    Route::post('training-schedule-import-excel-default', [TrainingScheduleController::class, 'importExcel'])->name('training-schedule.import-excel-default');

    // Route Training Unplane
    Route::resource('training-unplan', TrainingUnplanController::class);
    Route::get('training-unplan-api', [TrainingUnplanController::class, 'indexApi'])->name('training-unplan.listapi');
    Route::get('training-unplan-export-pdf-default', [TrainingUnplanController::class, 'exportPdf'])->name('training-unplan.export-pdf-default');
    Route::get('training-unplan-export-excel-default', [TrainingUnplanController::class, 'exportExcel'])->name('training-unplan.export-excel-default');
    Route::post('training-unplan-import-excel-default', [TrainingUnplanController::class, 'importExcel'])->name('training-unplan.import-excel-default');

    // Route Training Unplane Participant
    Route::resource('training-unplan-participant', TrainingUnplanParticipantController::class);
    Route::get('training-unplan-participant-api', [TrainingUnplanParticipantController::class, 'indexApi'])->name('training-unplan-participant.listapi');
    Route::get('training-unplan-participant-export-pdf-default', [TrainingUnplanParticipantController::class, 'exportPdf'])->name('training-unplan-participant.export-pdf-default');
    Route::get('training-unplan-participant-export-excel-default', [TrainingUnplanParticipantController::class, 'exportExcel'])->name('training-unplan-participant.export-excel-default');
    Route::post('training-unplan-participant-import-excel-default', [TrainingUnplanParticipantController::class, 'importExcel'])->name('training-unplan-participant.import-excel-default');

    // Route Event
    Route::resource('event', EventController::class);
    Route::get('event-api', [EventController::class, 'indexApi'])->name('event.listapi');
    Route::get('event-export-pdf-default', [EventController::class, 'exportPdf'])->name('event.export-pdf-default');
    Route::get('event-export-excel-default', [EventController::class, 'exportExcel'])->name('event.export-excel-default');
    Route::post('event-import-excel-default', [EventController::class, 'importExcel'])->name('event.import-excel-default');
    Route::post('/attendance/import-pdf', [EventController::class, 'importPdf'])->name('attachments.importPdf');

    // Route Trainer
    Route::resource('trainer', TrainerController::class);
    Route::get('trainer-api', [TrainerController::class, 'indexApi'])->name('trainer.listapi');
    Route::get('trainer-export-pdf-default', [TrainerController::class, 'exportPdf'])->name('trainer.export-pdf-default');
    Route::get('trainer-export-excel-default', [TrainerController::class, 'exportExcel'])->name('trainer.export-excel-default');
    Route::post('trainer-import-excel-default', [TrainerController::class, 'importExcel'])->name('trainer.import-excel-default');

    // Route Participant
    Route::resource('participant', ParticipantController::class);
    Route::get('participant-api', [ParticipantController::class, 'indexApi'])->name('participant.listapi');
    Route::get('participant-export-pdf-default', [ParticipantController::class, 'exportPdf'])->name('participant.export-pdf-default');
    Route::get('participant-export-excel-default', [ParticipantController::class, 'exportExcel'])->name('participant.export-excel-default');
    Route::post('participant-import-excel-default', [ParticipantController::class, 'importExcel'])->name('participant.import-excel-default');

    // Route Attendance
    Route::resource('attendance', AttendanceController::class);
    Route::get('attendance-api', [AttendanceController::class, 'indexApi'])->name('attendance.listapi');
    Route::get('attendance-export-pdf-default', [AttendanceController::class, 'exportPdf'])->name('attendance.export-pdf-default');
    Route::get('attendance-export-excel-default', [AttendanceController::class, 'exportExcel'])->name('attendance.export-excel-default');
    Route::post('attendance-import-excel-default', [AttendanceController::class, 'importExcel'])->name('attendance.import-excel-default');

    // Route Materi
    Route::resource('materi', MateriController::class);
    Route::get('materi-api', [MateriController::class, 'indexApi'])->name('materi.listapi');
    Route::get('materi-export-pdf-default', [MateriController::class, 'exportPdf'])->name('materi.export-pdf-default');
    Route::get('materi-export-excel-default', [MateriController::class, 'exportExcel'])->name('materi.export-excel-default');
    Route::post('materi-import-excel-default', [MateriController::class, 'importExcel'])->name('materi.import-excel-default');

    // Route Materi Log
    Route::resource('materi-log', MateriLogController::class);
    Route::get('materi-log-api', [MateriLogController::class, 'indexApi'])->name('materi-log.listapi');
    Route::get('materi-log-export-pdf-default', [MateriLogController::class, 'exportPdf'])->name('materi-log.export-pdf-default');
    Route::get('materi-log-export-excel-default', [MateriLogController::class, 'exportExcel'])->name('materi-log.export-excel-default');
    Route::post('materi-log-import-excel-default', [MateriLogController::class, 'importExcel'])->name('materi-log.import-excel-default');

    // Route Question
    Route::resource('question', QuestionController::class);
    Route::get('question-api', [QuestionController::class, 'indexApi'])->name('question.listapi');
    Route::get('question-export-pdf-default', [QuestionController::class, 'exportPdf'])->name('question.export-pdf-default');
    Route::get('question-export-excel-default', [QuestionController::class, 'exportExcel'])->name('question.export-excel-default');
    Route::post('question-import-excel-default', [QuestionController::class, 'importExcel'])->name('question.import-excel-default');

    // Route Answer
    Route::resource('answer', AnswerController::class);
    Route::get('answer-api', [AnswerController::class, 'indexApi'])->name('answer.listapi');
    Route::get('answer-export-pdf-default', [AnswerController::class, 'exportPdf'])->name('answer.export-pdf-default');
    Route::get('answer-export-excel-default', [AnswerController::class, 'exportExcel'])->name('answer.export-excel-default');
    Route::post('answer-import-excel-default', [AnswerController::class, 'importExcel'])->name('answer.import-excel-default');

    // Route Result Question
    Route::resource('result-question', ResultQuestionController::class);
    Route::get('result-question-api', [ResultQuestionController::class, 'indexApi'])->name('result-question.listapi');
    Route::get('result-question-export-pdf-default', [ResultQuestionController::class, 'exportPdf'])->name('result-question.export-pdf-default');
    Route::get('result-question-export-excel-default', [ResultQuestionController::class, 'exportExcel'])->name('result-question.export-excel-default');
    Route::post('result-question-import-excel-default', [ResultQuestionController::class, 'importExcel'])->name('result-question.import-excel-default');

    // Route Answer Participant
    Route::resource('answer-participant', AnswerParticipantController::class);
    Route::get('answer-participant-api', [AnswerParticipantController::class, 'indexApi'])->name('answer-participant.listapi');
    Route::get('answer-participant-export-pdf-default', [AnswerParticipantController::class, 'exportPdf'])->name('answer-participant.export-pdf-default');
    Route::get('answer-participant-export-excel-default', [AnswerParticipantController::class, 'exportExcel'])->name('answer-participant.export-excel-default');
    Route::post('answer-participant-import-excel-default', [AnswerParticipantController::class, 'importExcel'])->name('answer-participant.import-excel-default');

    // Route Template Certification
    Route::resource('template-certification', TemplateCertificationController::class);
    Route::get('template-certification-api', [TemplateCertificationController::class, 'indexApi'])->name('template-certification.listapi');
    Route::get('template-certification-export-pdf-default', [TemplateCertificationController::class, 'exportPdf'])->name('template-certification.export-pdf-default');
    Route::get('template-certification-export-excel-default', [TemplateCertificationController::class, 'exportExcel'])->name('template-certification.export-excel-default');
    Route::post('template-certification-import-excel-default', [TemplateCertificationController::class, 'importExcel'])->name('template-certification.import-excel-default');

    // Route Certification
    Route::resource('certification', CertificationController::class);
    Route::get('certification-api', [CertificationController::class, 'indexApi'])->name('certification.listapi');
    Route::get('certification-export-pdf-default', [CertificationController::class, 'exportPdf'])->name('certification.export-pdf-default');
    Route::get('certification-export-excel-default', [CertificationController::class, 'exportExcel'])->name('certification.export-excel-default');
    Route::post('certification-import-excel-default', [CertificationController::class, 'importExcel'])->name('certification.import-excel-default');

    // Route Evaluation
    Route::resource('evaluation', EvaluationController::class);
    Route::get('evaluation-api', [EvaluationController::class, 'indexApi'])->name('evaluation.listapi');
    Route::get('evaluation-export-pdf-default', [EvaluationController::class, 'exportPdf'])->name('evaluation.export-pdf-default');
    Route::get('evaluation-export-excel-default', [EvaluationController::class, 'exportExcel'])->name('evaluation.export-excel-default');
    Route::post('evaluation-import-excel-default', [EvaluationController::class, 'importExcel'])->name('evaluation.import-excel-default');

    // Route Documentation
    Route::resource('documentation', DocumentationController::class);
    Route::get('documentation-api', [DocumentationController::class, 'indexApi'])->name('documentation.listapi');
    Route::get('documentation-export-pdf-default', [DocumentationController::class, 'exportPdf'])->name('documentation.export-pdf-default');
    Route::get('documentation-export-excel-default', [DocumentationController::class, 'exportExcel'])->name('documentation.export-excel-default');
    Route::post('documentation-import-excel-default', [DocumentationController::class, 'importExcel'])->name('documentation.import-excel-default');

    // Route Report Training
    Route::resource('report-training', ReportTrainingController::class);
    Route::get('report-training-api', [ReportTrainingController::class, 'indexApi'])->name('report-training.listapi');
    Route::get('report-training-export-pdf-default', [ReportTrainingController::class, 'exportPdf'])->name('report-training.export-pdf-default');
    Route::get('report-training-export-excel-default', [ReportTrainingController::class, 'exportExcel'])->name('report-training.export-excel-default');
    Route::post('report-training-import-excel-default', [ReportTrainingController::class, 'importExcel'])->name('report-training.import-excel-default');
    Route::get('director-report-signature', [ReportTrainingController::class, 'signatureExternal'])->name('director.signature.external');
    Route::post('director-report-signature', [ReportTrainingController::class, 'storeSignature'])->name('director.signature.store');

    // Route Resume Materi
    Route::resource('resume-materi', ResumeMateriController::class);
    Route::get('resume-materi-api', [ResumeMateriController::class, 'indexApi'])->name('resume-materi.listapi');
    Route::get('resume-materi-export-pdf-default', [ResumeMateriController::class, 'exportPdf'])->name('resume-materi.export-pdf-default');
    Route::get('resume-materi-export-excel-default', [ResumeMateriController::class, 'exportExcel'])->name('resume-materi.export-excel-default');
    Route::post('resume-materi-import-excel-default', [ResumeMateriController::class, 'importExcel'])->name('resume-materi.import-excel-default');

    // Route Certification External
    Route::resource('certification-external', CertificationExternalController::class);
    Route::get('certification-external-api', [CertificationExternalController::class, 'indexApi'])->name('certification-external.listapi');
    Route::get('certification-external-export-pdf-default', [CertificationExternalController::class, 'exportPdf'])->name('certification-external.export-pdf-default');
    Route::get('certification-external-export-excel-default', [CertificationExternalController::class, 'exportExcel'])->name('certification-external.export-excel-default');
    Route::post('certification-external-import-excel-default', [CertificationExternalController::class, 'importExcel'])->name('certification-external.import-excel-default');

    // Route Matrik Pelatihan
    Route::resource('matrik', MatrikController::class);
    Route::get('matrik-api', [MatrikController::class, 'indexApi'])->name('matrik.listapi');
    Route::get('matrik-export-pdf-default', [MatrikController::class, 'exportPdf'])->name('matrik.export-pdf-default');
    Route::get('matrik-export-excel-default', [MatrikController::class, 'exportExcel'])->name('matrik.export-excel-default');
    Route::post('matrik-import-excel-default', [MatrikController::class, 'importExcel'])->name('matrik.import-excel-default');

    // Route Approval
    Route::resource('approval', ApprovalController::class);
    Route::get('approval-api', [ApprovalController::class, 'indexApi'])->name('approval.listapi');
    Route::get('approval-export-pdf-default', [ApprovalController::class, 'exportPdf'])->name('approval.export-pdf-default');
    Route::get('approval-export-excel-default', [ApprovalController::class, 'exportExcel'])->name('approval.export-excel-default');
    Route::post('approval-import-excel-default', [ApprovalController::class, 'importExcel'])->name('approval.import-excel-default');
    
    // Route Director Signature
    Route::resource('director-signature', DirectorSignatureController::class);
    Route::get('director-signature-api', [DirectorSignatureController::class, 'indexApi'])->name('director-signature.listapi');
    Route::get('director-signature-export-pdf-default', [DirectorSignatureController::class, 'exportPdf'])->name('director-signature.export-pdf-default');
    Route::get('director-signature-export-excel-default', [DirectorSignatureController::class, 'exportExcel'])->name('director-signature.export-excel-default');
    Route::post('director-signature-import-excel-default', [DirectorSignatureController::class, 'importExcel'])->name('director-signature.import-excel-default');
});


// Custome Route
Route::group(['middleware' => ['web', 'auth']], function () {
    // Route Dashboard
    Route::get('/qrcode-scanner', [DashboardController::class, 'qrcodeScanner']);

    // Route User
    Route::get('my-account', [UserController::class, 'profile']);
    Route::post('update-profile', [UserController::class, 'updateProfile']);
    Route::get('signature-bulk-update', [UserController::class, 'signatureBulkUpdate'])->name('user.signature.bulk.update');
    Route::post('users/bulk-update-signatures', [UserController::class, 'bulkUpdateSignatures'])
        ->name('users.bulk.update.signatures');

    // Route Training
    Route::post('training/{id}', [TrainingController::class, 'approve'])->name('training.approve');

    // Route Training Analyst
    Route::get('training-analyst-form', [TrainingAnalystController::class, 'trainingForm'])->name('training-analyst.form');
    Route::post('training-analyst/save-all', [TrainingAnalystController::class, 'saveAll'])->name('training-analyst.saveAll');
    Route::get('training-analyst-pdf', [TrainingAnalystController::class, 'generatePDF'])->name('training-analyst.pdf');
    Route::post('training-analyst/{id}', [TrainingAnalystController::class, 'approve'])->name('training.analyst.approve');


    
    Route::post('approval-data/{id}', [ApprovalController::class, 'approve'])->name('approval.data');


    // Route Training Participant
    Route::get('participant-ajax', [TrainingParticipantController::class, 'participantAjax']);

    // Route Training Need
    Route::post('training-need/{id}', [TrainingNeedController::class, 'approve'])->name('training.need.approve');
    Route::get('training-need-pdf', [TrainingNeedController::class, 'generatePDF'])->name('training-need.pdf');

    

    // Route Training Unplan
    Route::post('training-unplan/{id}', [TrainingUnplanController::class, 'approve'])->name('training.unplan.approve');

    // Route Participant
    Route::post('participant-generate-user/{event_id}', [ParticipantController::class, 'generateUser'])->name('participant.generate.user');

    // Route Attendance
    Route::get('attendance-participant', [AttendanceController::class, 'attendance'])->name('participant.attendance');
    Route::get('checkout-participant', [AttendanceController::class, 'checkout'])->name('participant.checkout');
    Route::post('participant-checkout/{token}', [AttendanceController::class, 'checkoutForm'])->name('participant.checkout.form');
    Route::post('participant-attendance/{token}', [AttendanceController::class, 'attendanceForm'])->name('participant.attendance.form');
    Route::post('participant-attendance-ready/', [AttendanceController::class, 'attendanceFormReady'])->name('participant.attendance.form.ready');
    
    Route::get('attendance-present-pdf', [AttendanceController::class, 'presentPdf'])->name('attendance.present.pdf');

    // Route Barcode
    Route::get('/set-account/{id}', function ($id) {
        session(['account_id' => $id]);
        return redirect('/participant-account');
    })->name('set.account');
    Route::get('/participant-account', [ParticipantController::class, 'participantAccount'])->name('participant.account');

    Route::get('/set-attendance/{id}', function ($id) {
        session(['attendance_id' => $id]);
        return redirect('/attendance-barcode');
    })->name('set.attendance');
    Route::get('/attendance-barcode', [AttendanceController::class, 'attendanceBarcode'])->name('attendance.barcode');

    Route::get('/set-question/{id}', function ($id) {
        session(['question_id' => $id]);
        return redirect('/question-barcode');
    })->name('set.question');
    Route::get('/question-barcode', [QuestionController::class, 'questionBarcode'])->name('question.barcode');

    Route::get('/set-checkout/{id}', function ($id) {
        session(['checkout_id' => $id]);
        return redirect('/checkout-barcode');
    })->name('set.checkout');
    Route::get('/checkout-barcode', [AttendanceController::class, 'checkoutBarcode'])->name('checkout.barcode');

    // Route Assesment
    Route::get('/api/questions', [QuestionController::class, 'getQuestionsForTest'])->name('api.questions');
    Route::post('/api/submit-test', [QuestionController::class, 'submitTest'])->name('api.submit-test');
    Route::get('/assesment', [AssesmentController::class, 'assesment'])->name('assesment');
    Route::get('/assesment-pdf', [AssesmentController::class, 'generatePDF'])->name('assesment.pdf');

    // Route Certification
    Route::get('certification-pdf', [CertificationController::class, 'generatePDF'])->name('certification.pdf');

    // Route Evaluation
    Route::get('/evaluation-pdf', [EvaluationController::class, 'generatePDF'])->name('evaluation.pdf');
    Route::get('/evaluation-form', [EvaluationController::class, 'evaluationForm'])->name('evaluation.form');
    Route::get('/evaluation-bulk', [EvaluationController::class, 'evaluationBulk'])->name('evaluation.bulk');
    Route::get('/evaluation-month', [EvaluationController::class, 'evaluationMonth'])->name('evaluation.month');
    Route::get('/evaluation-month-result', [EvaluationController::class, 'evaluationMonthResult'])->name('evaluation.month.result');
    Route::post('/submit-evaluation', [EvaluationController::class, 'submitEvaluation'])->name('submit.evaluation');
    Route::post('/submit-evaluation-month', [EvaluationController::class, 'submitEvaluationMonth'])->name('submit.evaluation.month');

    // Route Documentation
    Route::get('documentation-pdf', [DocumentationController::class, 'generatePDF'])->name('documentation.pdf');

    // Route Report Training
    Route::get('report-training/{report}/pdf', [ReportTrainingController::class, 'generatePDF'])->name('report-training.pdf');

    // Route Event Approve
    Route::post('event/{id}', [EventController::class, 'approve'])->name('event.approve');
    
    // Route Certification Approve
    Route::post('certification/{id}', [CertificationController::class, 'approve'])->name('certification.approve');

    // Route Report Training Approve
    Route::post('report-training/{id}', [ReportTrainingController::class, 'approve'])->name('report-training.approve');
});

// Route Training Schedule
    Route::get('training-schedule-pdf', [TrainingScheduleController::class, 'generatePDF'])->name('training-schedule.pdf');
    Route::get('attendance-ready-pdf', [AttendanceController::class, 'readyPdf'])->name('attendance.ready.pdf');
    Route::get('/report/workshops', [WorkshopController::class, 'dataTable'])->name('report.workshops');


    Route::group(['middleware' => ['web', 'auth']], function () {
        // Route Company
        Route::resource('company', CompanyController::class);
        Route::get('company-api', [CompanyController::class, 'indexApi'])->name('company.listapi');
        Route::get('company-export-pdf-default', [CompanyController::class, 'exportPdf'])->name('company.export-pdf-default');
        Route::get('company-export-excel-default', [CompanyController::class, 'exportExcel'])->name('company.export-excel-default');
        Route::post('company-import-excel-default', [CompanyController::class, 'importExcel'])->name('company.import-excel-default');

        // Route Division
        Route::resource('division', DivisionController::class);
        Route::get('division-api', [DivisionController::class, 'indexApi'])->name('division.listapi');
        Route::get('division-export-pdf-default', [DivisionController::class, 'exportPdf'])->name('division.export-pdf-default');
        Route::get('division-export-excel-default', [DivisionController::class, 'exportExcel'])->name('division.export-excel-default');
        Route::post('division-import-excel-default', [DivisionController::class, 'importExcel'])->name('division.import-excel-default');

        // Route Department
        Route::resource('department', DepartmentController::class);
        Route::get('department-api', [DepartmentController::class, 'indexApi'])->name('department.listapi');
        Route::get('department-export-pdf-default', [DepartmentController::class, 'exportPdf'])->name('department.export-pdf-default');
        Route::get('department-export-excel-default', [DepartmentController::class, 'exportExcel'])->name('department.export-excel-default');
        Route::post('department-import-excel-default', [DepartmentController::class, 'importExcel'])->name('department.import-excel-default');

        // Route Position
        Route::resource('position', PositionController::class);
        Route::get('position-api', [PositionController::class, 'indexApi'])->name('position.listapi');
        Route::get('position-export-pdf-default', [PositionController::class, 'exportPdf'])->name('position.export-pdf-default');
        Route::get('position-export-excel-default', [PositionController::class, 'exportExcel'])->name('position.export-excel-default');
        Route::post('position-import-excel-default', [PositionController::class, 'importExcel'])->name('position.import-excel-default');

        // Route Section
        Route::resource('section', SectionController::class);
        Route::get('section-api', [SectionController::class, 'indexApi'])->name('section.listapi');
        Route::get('section-export-pdf-default', [SectionController::class, 'exportPdf'])->name('section.export-pdf-default');
        Route::get('section-export-excel-default', [SectionController::class, 'exportExcel'])->name('section.export-excel-default');
        Route::post('section-import-excel-default', [SectionController::class, 'importExcel'])->name('section.import-excel-default');

        // Route Group
        Route::resource('group', GroupController::class);
        Route::get('group-api', [GroupController::class, 'indexApi'])->name('group.listapi');
        Route::get('group-export-pdf-default', [GroupController::class, 'exportPdf'])->name('group.export-pdf-default');
        Route::get('group-export-excel-default', [GroupController::class, 'exportExcel'])->name('group.export-excel-default');
        Route::post('group-import-excel-default', [GroupController::class, 'importExcel'])->name('group.import-excel-default');

        // Route Employee
        Route::resource('employee', EmployeeController::class);
        Route::get('employee-api', [EmployeeController::class, 'indexApi'])->name('employee.listapi');
        Route::get('employee-export-pdf-default', [EmployeeController::class, 'exportPdf'])->name('employee.export-pdf-default');
        Route::get('employee-export-excel-default', [EmployeeController::class, 'exportExcel'])->name('employee.export-excel-default');
        Route::post('employee-import-excel-default', [EmployeeController::class, 'importExcel'])->name('employee.import-excel-default');

        // Route Workshops
        Route::resource('workshop', WorkshopController::class);
        Route::get('workshop-api', [WorkshopController::class, 'indexApi'])->name('workshop.listapi');
        Route::get('workshop-export-pdf-default', [WorkshopController::class, 'exportPdf'])->name('workshop.export-pdf-default');
        Route::get('workshop-export-excel-default', [WorkshopController::class, 'exportExcel'])->name('workshop.export-excel-default');
        Route::post('workshop-import-excel-default', [WorkshopController::class, 'importExcel'])->name('workshop.import-excel-default');

        // Route Training Location
        Route::resource('training-location', TrainingLocationController::class);
        Route::get('training-location-api', [TrainingLocationController::class, 'indexApi'])->name('training-location.listapi');
        Route::get('training-location-export-pdf-default', [TrainingLocationController::class, 'exportPdf'])->name('training-location.export-pdf-default');
        Route::get('training-location-export-excel-default', [TrainingLocationController::class, 'exportExcel'])->name('training-location.export-excel-default');
        Route::post('training-location-import-excel-default', [TrainingLocationController::class, 'importExcel'])->name('training-location.import-excel-default');

        // Route Jobdesc Section
        Route::resource('jobdesc-section', JobdescSectionController::class);
        Route::get('jobdesc-section-api', [JobdescSectionController::class, 'indexApi'])->name('jobdesc-section.listapi');
        Route::get('jobdesc-section-export-pdf-default', [JobdescSectionController::class, 'exportPdf'])->name('jobdesc-section.export-pdf-default');
        Route::get('jobdesc-section-export-excel-default', [JobdescSectionController::class, 'exportExcel'])->name('jobdesc-section.export-excel-default');
        Route::post('jobdesc-section-import-excel-default', [JobdescSectionController::class, 'importExcel'])->name('jobdesc-section.import-excel-default');

        Route::get('/report/jobdesc-section',       [JobdescSectionController::class, 'jobdescSection']);
        Route::post('/section/jobdesc/test',             [JobdescSectionController::class, 'storeData']);
        
        Route::put('/section/jobdesc/{id}',         [JobdescSectionController::class, 'updateData']);
        Route::delete('/section/jobdesc/{id}',      [JobdescSectionController::class, 'destroyData']);
        Route::get('/section/jobdesc/{id}',         [JobdescSectionController::class, 'showData']);
        
    });