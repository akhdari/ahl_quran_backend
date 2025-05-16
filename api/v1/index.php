<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	header("Content-Type: application/json");
	header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
	require_once './router.php';
	require_once '../../db.php';
	require_once '../../controllers/Controller.php';

	require_once '../../controllers/AccountInfoController.php';
	require_once '../../controllers/AppreciationController.php';
	require_once '../../controllers/ContactInfoController.php';
	require_once '../../controllers/Controller.php';
	require_once '../../controllers/ExamController.php';
	require_once '../../controllers/ExamLevelController.php';
	require_once '../../controllers/ExamStudentController.php';
	require_once '../../controllers/ExamTeacherController.php';
	require_once '../../controllers/FormalEducationInfoController.php';
	require_once '../../controllers/GoldenRecordController.php';
	require_once '../../controllers/GuardianController.php';
	require_once '../../controllers/LectureContentController.php';
	require_once '../../controllers/LectureController.php';
	require_once '../../controllers/LectureStudentController.php';
	require_once '../../controllers/LectureTeacherController.php';
	require_once '../../controllers/MedicalInfoController.php';
	require_once '../../controllers/PersonalInfoController.php';
	require_once '../../controllers/RequestCopyController.php';
	require_once '../../controllers/StudentController.php';
	require_once '../../controllers/SubscriptionInfoController.php';
	require_once '../../controllers/SupervisorController.php';
	require_once '../../controllers/TeacherController.php';
	require_once '../../controllers/TeamAccomplishmentController.php';
	require_once '../../controllers/TeamAccomplishmentStudentController.php';
	require_once '../../controllers/WeeklyScheduleController.php';


	Controller::setDbConnection((new DB("localhost","root","","quran")));
	$router = Router::getInstance("/dev_run/Project/ahl_quran_backend");
	$router->setPrefix('/api/v1');


	$router->get('/', fn()=> var_dump(json_encode(['response' => 'API v1'])));



	// Routes for AccountInfoController
	$router->get('/accountinfos',  ['AccountInfoController', 'getAll']);
	$router->get('/accountinfos/:id',  ['AccountInfoController', 'getOne']);
	$router->post('/accountinfos',  ['AccountInfoController', 'save']);
	$router->put('/accountinfos/:id',  ['AccountInfoController', 'edit']);
	$router->patch('/accountinfos/:id',  ['AccountInfoController', 'update']);
	$router->delete('/accountinfos/:id',  ['AccountInfoController', 'delete']);


	// Routes for AppreciationController
	$router->get('/appreciations',  ['AppreciationController', 'getAll']);
	$router->get('/appreciations/:id',  ['AppreciationController', 'getOne']);
	$router->post('/appreciations',  ['AppreciationController', 'save']);
	$router->put('/appreciations/:id',  ['AppreciationController', 'edit']);
	$router->patch('/appreciations/:id',  ['AppreciationController', 'update']);
	$router->delete('/appreciations/:id',  ['AppreciationController', 'delete']);


	// Routes for ContactInfoController
	$router->get('/contactinfos',  ['ContactInfoController', 'getAll']);
	$router->get('/contactinfos/:id',  ['ContactInfoController', 'getOne']);
	$router->post('/contactinfos',  ['ContactInfoController', 'save']);
	$router->put('/contactinfos/:id',  ['ContactInfoController', 'edit']);
	$router->patch('/contactinfos/:id',  ['ContactInfoController', 'update']);
	$router->delete('/contactinfos/:id',  ['ContactInfoController', 'delete']);


	// Routes for ExamController
	$router->get('/exams',  ['ExamController', 'getAll']);
	$router->get('/exams/:id',  ['ExamController', 'getOne']);
	$router->post('/exams',  ['ExamController', 'save']);
	$router->put('/exams/:id',  ['ExamController', 'edit']);
	$router->patch('/exams/:id',  ['ExamController', 'update']);
	$router->delete('/exams/:id',  ['ExamController', 'delete']);


	// Routes for ExamLevelController
	$router->get('/examlevels',  ['ExamLevelController', 'getAll']);
	$router->get('/examlevels/:id',  ['ExamLevelController', 'getOne']);
	$router->post('/examlevels',  ['ExamLevelController', 'save']);
	$router->put('/examlevels/:id',  ['ExamLevelController', 'edit']);
	$router->patch('/examlevels/:id',  ['ExamLevelController', 'update']);
	$router->delete('/examlevels/:id',  ['ExamLevelController', 'delete']);


	// Routes for ExamStudentController
	$router->get('/examstudents',  ['ExamStudentController', 'getAll']);
	$router->get('/examstudents/:id',  ['ExamStudentController', 'getOne']);
	$router->post('/examstudents',  ['ExamStudentController', 'save']);
	$router->put('/examstudents/:id',  ['ExamStudentController', 'edit']);
	$router->patch('/examstudents/:id',  ['ExamStudentController', 'update']);
	$router->delete('/examstudents/:id',  ['ExamStudentController', 'delete']);


	// Routes for ExamTeacherController
	$router->get('/examteachers',  ['ExamTeacherController', 'getAll']);
	$router->get('/examteachers/:id',  ['ExamTeacherController', 'getOne']);
	$router->post('/examteachers',  ['ExamTeacherController', 'save']);
	$router->put('/examteachers/:id',  ['ExamTeacherController', 'edit']);
	$router->patch('/examteachers/:id',  ['ExamTeacherController', 'update']);
	$router->delete('/examteachers/:id',  ['ExamTeacherController', 'delete']);


	// Routes for FormalEducationInfoController
	$router->get('/formaleducationinfos',  ['FormalEducationInfoController', 'getAll']);
	$router->get('/formaleducationinfos/:id',  ['FormalEducationInfoController', 'getOne']);
	$router->post('/formaleducationinfos',  ['FormalEducationInfoController', 'save']);
	$router->put('/formaleducationinfos/:id',  ['FormalEducationInfoController', 'edit']);
	$router->patch('/formaleducationinfos/:id',  ['FormalEducationInfoController', 'update']);
	$router->delete('/formaleducationinfos/:id',  ['FormalEducationInfoController', 'delete']);


	// Routes for GoldenRecordController
	$router->get('/goldenrecords',  ['GoldenRecordController', 'getAll']);
	$router->get('/goldenrecords/:id',  ['GoldenRecordController', 'getOne']);
	$router->post('/goldenrecords',  ['GoldenRecordController', 'save']);
	$router->put('/goldenrecords/:id',  ['GoldenRecordController', 'edit']);
	$router->patch('/goldenrecords/:id',  ['GoldenRecordController', 'update']);
	$router->delete('/goldenrecords/:id',  ['GoldenRecordController', 'delete']);


	// Routes for GuardianController
	$router->get('/guardians',  ['GuardianController', 'getAll']);
	$router->get('/guardians/:id',  ['GuardianController', 'getOne']);
	$router->post('/guardians',  ['GuardianController', 'save']);
	$router->put('/guardians/:id',  ['GuardianController', 'edit']);
	$router->patch('/guardians/:id',  ['GuardianController', 'update']);
	$router->delete('/guardians/:id',  ['GuardianController', 'delete']);


	// Routes for LectureContentController
	$router->get('/lecturecontents',  ['LectureContentController', 'getAll']);
	$router->get('/lecturecontents/:id',  ['LectureContentController', 'getOne']);
	$router->post('/lecturecontents',  ['LectureContentController', 'save']);
	$router->put('/lecturecontents/:id',  ['LectureContentController', 'edit']);
	$router->patch('/lecturecontents/:id',  ['LectureContentController', 'update']);
	$router->delete('/lecturecontents/:id',  ['LectureContentController', 'delete']);


	// Routes for LectureController
	$router->get('/lectures',  ['LectureController', 'getAll']);
	$router->get('/lectures/:id',  ['LectureController', 'getOne']);
	$router->post('/lectures',  ['LectureController', 'save']);
	$router->put('/lectures/:id',  ['LectureController', 'edit']);
	$router->patch('/lectures/:id',  ['LectureController', 'update']);
	$router->delete('/lectures/:id',  ['LectureController', 'delete']);


	// Routes for LectureStudentController
	$router->get('/lecturestudents',  ['LectureStudentController', 'getAll']);
	$router->get('/lecturestudents/:id',  ['LectureStudentController', 'getOne']);
	$router->post('/lecturestudents',  ['LectureStudentController', 'save']);
	$router->put('/lecturestudents/:id',  ['LectureStudentController', 'edit']);
	$router->patch('/lecturestudents/:id',  ['LectureStudentController', 'update']);
	$router->delete('/lecturestudents/:id',  ['LectureStudentController', 'delete']);


	// Routes for LectureTeacherController
	$router->get('/lectureteachers',  ['LectureTeacherController', 'getAll']);
	$router->get('/lectureteachers/:id',  ['LectureTeacherController', 'getOne']);
	$router->post('/lectureteachers',  ['LectureTeacherController', 'save']);
	$router->put('/lectureteachers/:id',  ['LectureTeacherController', 'edit']);
	$router->patch('/lectureteachers/:id',  ['LectureTeacherController', 'update']);
	$router->delete('/lectureteachers/:id',  ['LectureTeacherController', 'delete']);


	// Routes for MedicalInfoController
	$router->get('/medicalinfos',  ['MedicalInfoController', 'getAll']);
	$router->get('/medicalinfos/:id',  ['MedicalInfoController', 'getOne']);
	$router->post('/medicalinfos',  ['MedicalInfoController', 'save']);
	$router->put('/medicalinfos/:id',  ['MedicalInfoController', 'edit']);
	$router->patch('/medicalinfos/:id',  ['MedicalInfoController', 'update']);
	$router->delete('/medicalinfos/:id',  ['MedicalInfoController', 'delete']);


	// Routes for PersonalInfoController
	$router->get('/personalinfos',  ['PersonalInfoController', 'getAll']);
	$router->get('/personalinfos/:id',  ['PersonalInfoController', 'getOne']);
	$router->post('/personalinfos',  ['PersonalInfoController', 'save']);
	$router->put('/personalinfos/:id',  ['PersonalInfoController', 'edit']);
	$router->patch('/personalinfos/:id',  ['PersonalInfoController', 'update']);
	$router->delete('/personalinfos/:id',  ['PersonalInfoController', 'delete']);


	// Routes for RequestCopyController
	$router->get('/requestcopys',  ['RequestCopyController', 'getAll']);
	$router->get('/requestcopys/:id',  ['RequestCopyController', 'getOne']);
	$router->post('/requestcopys',  ['RequestCopyController', 'save']);
	$router->put('/requestcopys/:id',  ['RequestCopyController', 'edit']);
	$router->patch('/requestcopys/:id',  ['RequestCopyController', 'update']);
	$router->delete('/requestcopys/:id',  ['RequestCopyController', 'delete']);


	// Routes for StudentController
	$router->get('/students',  ['StudentController', 'getAll']);
	$router->get('/students/:id',  ['StudentController', 'getOne']);
	$router->post('/students',  ['StudentController', 'save']);
	$router->put('/students/:id',  ['StudentController', 'edit']);
	$router->patch('/students/:id',  ['StudentController', 'update']);
	$router->delete('/students/:id',  ['StudentController', 'delete']);


	// Routes for SubscriptionInfoController
	$router->get('/subscriptioninfos',  ['SubscriptionInfoController', 'getAll']);
	$router->get('/subscriptioninfos/:id',  ['SubscriptionInfoController', 'getOne']);
	$router->post('/subscriptioninfos',  ['SubscriptionInfoController', 'save']);
	$router->put('/subscriptioninfos/:id',  ['SubscriptionInfoController', 'edit']);
	$router->patch('/subscriptioninfos/:id',  ['SubscriptionInfoController', 'update']);
	$router->delete('/subscriptioninfos/:id',  ['SubscriptionInfoController', 'delete']);


	// Routes for SupervisorController
	$router->get('/supervisors',  ['SupervisorController', 'getAll']);
	$router->get('/supervisors/:id',  ['SupervisorController', 'getOne']);
	$router->post('/supervisors',  ['SupervisorController', 'save']);
	$router->put('/supervisors/:id',  ['SupervisorController', 'edit']);
	$router->patch('/supervisors/:id',  ['SupervisorController', 'update']);
	$router->delete('/supervisors/:id',  ['SupervisorController', 'delete']);


	// Routes for TeacherController
	$router->get('/teachers',  ['TeacherController', 'getAll']);
	$router->get('/teachers/:id',  ['TeacherController', 'getOne']);
	$router->post('/teachers',  ['TeacherController', 'save']);
	$router->put('/teachers/:id',  ['TeacherController', 'edit']);
	$router->patch('/teachers/:id',  ['TeacherController', 'update']);
	$router->delete('/teachers/:id',  ['TeacherController', 'delete']);


	// Routes for TeamAccomplishmentController
	$router->get('/teamaccomplishments',  ['TeamAccomplishmentController', 'getAll']);
	$router->get('/teamaccomplishments/:id',  ['TeamAccomplishmentController', 'getOne']);
	$router->post('/teamaccomplishments',  ['TeamAccomplishmentController', 'save']);
	$router->put('/teamaccomplishments/:id',  ['TeamAccomplishmentController', 'edit']);
	$router->patch('/teamaccomplishments/:id',  ['TeamAccomplishmentController', 'update']);
	$router->delete('/teamaccomplishments/:id',  ['TeamAccomplishmentController', 'delete']);


	// Routes for TeamAccomplishmentStudentController
	$router->get('/teamaccomplishmentstudents',  ['TeamAccomplishmentStudentController', 'getAll']);
	$router->get('/teamaccomplishmentstudents/:id',  ['TeamAccomplishmentStudentController', 'getOne']);
	$router->post('/teamaccomplishmentstudents',  ['TeamAccomplishmentStudentController', 'save']);
	$router->put('/teamaccomplishmentstudents/:id',  ['TeamAccomplishmentStudentController', 'edit']);
	$router->patch('/teamaccomplishmentstudents/:id',  ['TeamAccomplishmentStudentController', 'update']);
	$router->delete('/teamaccomplishmentstudents/:id',  ['TeamAccomplishmentStudentController', 'delete']);


	// Routes for WeeklyScheduleController
	$router->get('/weeklyschedules',  ['WeeklyScheduleController', 'getAll']);
	$router->get('/weeklyschedules/:id',  ['WeeklyScheduleController', 'getOne']);
	$router->post('/weeklyschedules',  ['WeeklyScheduleController', 'save']);
	$router->put('/weeklyschedules/:id',  ['WeeklyScheduleController', 'edit']);
	$router->patch('/weeklyschedules/:id',  ['WeeklyScheduleController', 'update']);
	$router->delete('/weeklyschedules/:id',  ['WeeklyScheduleController', 'delete']);


$router->run(); // Initialize database connection
?>
