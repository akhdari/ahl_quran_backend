# API Documentation

## Base URL

`/api/v1`

---

## General

- **GET `/api/v1/`**  
  Returns the API version.

---

## Resources

For each resource below, the following RESTful endpoints are available unless otherwise noted.  
**All endpoints are prefixed with `/api/v1`.**  
**Use the specified HTTP method to call each endpoint.**

| Method | Endpoint                                 | Description                    |
|--------|------------------------------------------|--------------------------------|
| GET    | `/api/v1/resource`                       | Get all items                  |
| GET    | `/api/v1/resource/:id`                   | Get a single item by ID        |
| POST   | `/api/v1/resource`                       | Create a new item              |
| PUT    | `/api/v1/resource/:id`                   | Replace an item by ID          |
| PATCH  | `/api/v1/resource/:id`                   | Update part of an item by ID   |
| DELETE | `/api/v1/resource/:id`                   | Delete an item by ID           |

---

### AccountInfo

- **GET** `/api/v1/accountinfos`
- **GET** `/api/v1/accountinfos/:id`
- **POST** `/api/v1/accountinfos`
- **PUT** `/api/v1/accountinfos/:id`
- **PATCH** `/api/v1/accountinfos/:id`
- **DELETE** `/api/v1/accountinfos/:id`

### Appreciation

- **GET** `/api/v1/appreciations`
- **GET** `/api/v1/appreciations/:id`
- **POST** `/api/v1/appreciations`
- **PUT** `/api/v1/appreciations/:id`
- **PATCH** `/api/v1/appreciations/:id`
- **DELETE** `/api/v1/appreciations/:id`

### ContactInfo

- **GET** `/api/v1/contactinfos`
- **GET** `/api/v1/contactinfos/:id`
- **POST** `/api/v1/contactinfos`
- **PUT** `/api/v1/contactinfos/:id`
- **PATCH** `/api/v1/contactinfos/:id`
- **DELETE** `/api/v1/contactinfos/:id`

### Exam

- **GET** `/api/v1/exams`
- **GET** `/api/v1/exams/:id`
- **POST** `/api/v1/exams`
- **PUT** `/api/v1/exams/:id`
- **PATCH** `/api/v1/exams/:id`
- **DELETE** `/api/v1/exams/:id`

### ExamLevel

- **GET** `/api/v1/examlevels`
- **GET** `/api/v1/examlevels/:id`
- **POST** `/api/v1/examlevels`
- **PUT** `/api/v1/examlevels/:id`
- **PATCH** `/api/v1/examlevels/:id`
- **DELETE** `/api/v1/examlevels/:id`

### ExamStudent

- **GET** `/api/v1/examstudents`
- **GET** `/api/v1/examstudents/:id`
- **POST** `/api/v1/examstudents`
- **PUT** `/api/v1/examstudents/:id`
- **PATCH** `/api/v1/examstudents/:id`
- **DELETE** `/api/v1/examstudents/:id`

### ExamTeacher

- **GET** `/api/v1/examteachers`
- **GET** `/api/v1/examteachers/:id`
- **POST** `/api/v1/examteachers`
- **PUT** `/api/v1/examteachers/:id`
- **PATCH** `/api/v1/examteachers/:id`
- **DELETE** `/api/v1/examteachers/:id`

### FormalEducationInfo

- **GET** `/api/v1/formaleducationinfos`
- **GET** `/api/v1/formaleducationinfos/:id`
- **POST** `/api/v1/formaleducationinfos`
- **PUT** `/api/v1/formaleducationinfos/:id`
- **PATCH** `/api/v1/formaleducationinfos/:id`
- **DELETE** `/api/v1/formaleducationinfos/:id`

### GoldenRecord

- **GET** `/api/v1/goldenrecords`
- **GET** `/api/v1/goldenrecords/:id`
- **POST** `/api/v1/goldenrecords`
- **PUT** `/api/v1/goldenrecords/:id`
- **PATCH** `/api/v1/goldenrecords/:id`
- **DELETE** `/api/v1/goldenrecords/:id`

### Guardian

- **GET** `/api/v1/guardians`
- **GET** `/api/v1/guardians/:id`
- **POST** `/api/v1/guardians`
- **PUT** `/api/v1/guardians/:id`
- **PATCH** `/api/v1/guardians/:id`
- **DELETE** `/api/v1/guardians/:id`

### LectureContent

- **GET** `/api/v1/lecturecontents`
- **GET** `/api/v1/lecturecontents/:id`
- **POST** `/api/v1/lecturecontents`
- **PUT** `/api/v1/lecturecontents/:id`
- **PATCH** `/api/v1/lecturecontents/:id`
- **DELETE** `/api/v1/lecturecontents/:id`

### Lecture

- **GET** `/api/v1/lectures`
- **GET** `/api/v1/lectures/:id`
- **POST** `/api/v1/lectures`
- **PUT** `/api/v1/lectures/:id`
- **PATCH** `/api/v1/lectures/:id`
- **DELETE** `/api/v1/lectures/:id`

### LectureStudent

- **GET** `/api/v1/lecturestudents`
- **GET** `/api/v1/lecturestudents/:id`
- **POST** `/api/v1/lecturestudents`
- **PUT** `/api/v1/lecturestudents/:id`
- **PATCH** `/api/v1/lecturestudents/:id`
- **DELETE** `/api/v1/lecturestudents/:id`

### LectureTeacher

- **GET** `/api/v1/lectureteachers`
- **GET** `/api/v1/lectureteachers/:id`
- **POST** `/api/v1/lectureteachers`
- **PUT** `/api/v1/lectureteachers/:id`
- **PATCH** `/api/v1/lectureteachers/:id`
- **DELETE** `/api/v1/lectureteachers/:id`

### MedicalInfo

- **GET** `/api/v1/medicalinfos`
- **GET** `/api/v1/medicalinfos/:id`
- **POST** `/api/v1/medicalinfos`
- **PUT** `/api/v1/medicalinfos/:id`
- **PATCH** `/api/v1/medicalinfos/:id`
- **DELETE** `/api/v1/medicalinfos/:id`

### PersonalInfo

- **GET** `/api/v1/personalinfos`
- **GET** `/api/v1/personalinfos/:id`
- **POST** `/api/v1/personalinfos`
- **PUT** `/api/v1/personalinfos/:id`
- **PATCH** `/api/v1/personalinfos/:id`
- **DELETE** `/api/v1/personalinfos/:id`

### RequestCopy

- **GET** `/api/v1/requestcopys`
- **GET** `/api/v1/requestcopys/:id`
- **POST** `/api/v1/requestcopys`
- **PUT** `/api/v1/requestcopys/:id`
- **PATCH** `/api/v1/requestcopys/:id`
- **DELETE** `/api/v1/requestcopys/:id`

### Student

- **GET** `/api/v1/students`
- **GET** `/api/v1/students/:id`
- **POST** `/api/v1/students`
- **PUT** `/api/v1/students/:id`
- **PATCH** `/api/v1/students/:id`
- **DELETE** `/api/v1/students/:id`

### SubscriptionInfo

- **GET** `/api/v1/subscriptioninfos`
- **GET** `/api/v1/subscriptioninfos/:id`
- **POST** `/api/v1/subscriptioninfos`
- **PUT** `/api/v1/subscriptioninfos/:id`
- **PATCH** `/api/v1/subscriptioninfos/:id`
- **DELETE** `/api/v1/subscriptioninfos/:id`

### Supervisor

- **GET** `/api/v1/supervisors`
- **GET** `/api/v1/supervisors/:id`
- **POST** `/api/v1/supervisors`
- **PUT** `/api/v1/supervisors/:id`
- **PATCH** `/api/v1/supervisors/:id`
- **DELETE** `/api/v1/supervisors/:id`

### Teacher

- **GET** `/api/v1/teachers`
- **GET** `/api/v1/teachers/:id`
- **POST** `/api/v1/teachers`
- **PUT** `/api/v1/teachers/:id`
- **PATCH** `/api/v1/teachers/:id`
- **DELETE** `/api/v1/teachers/:id`

### TeamAccomplishment

- **GET** `/api/v1/teamaccomplishments`
- **GET** `/api/v1/teamaccomplishments/:id`
- **POST** `/api/v1/teamaccomplishments`
- **PUT** `/api/v1/teamaccomplishments/:id`
- **PATCH** `/api/v1/teamaccomplishments/:id`
- **DELETE** `/api/v1/teamaccomplishments/:id`

### TeamAccomplishmentStudent

- **GET** `/api/v1/teamaccomplishmentstudents`
- **GET** `/api/v1/teamaccomplishmentstudents/:id`
- **POST** `/api/v1/teamaccomplishmentstudents`
- **PUT** `/api/v1/teamaccomplishmentstudents/:id`
- **PATCH** `/api/v1/teamaccomplishmentstudents/:id`
- **DELETE** `/api/v1/teamaccomplishmentstudents/:id`

### WeeklySchedule

- **GET** `/api/v1/weeklyschedules`
- **GET** `/api/v1/weeklyschedules/:id`
- **POST** `/api/v1/weeklyschedules`
- **PUT** `/api/v1/weeklyschedules/:id`
- **PATCH** `/api/v1/weeklyschedules/:id`
- **DELETE** `/api/v1/weeklyschedules/:id`

---

## Notes

- All endpoints return JSON.
- Use appropriate HTTP methods for each action.
- For details on request/response bodies, refer to the controller/model for each resource.
