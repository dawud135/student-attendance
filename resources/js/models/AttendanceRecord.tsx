import User from "./User";

export default interface AttendanceRecord {
    id: number;
    user_id: number;
    teacher_id: number;
    school_class_id: number;
    school_subject_id: number;
    grade: string;
    date: string;
    status: string;
    minutes_late: number;
    reason: string;

    user?: User;
    teacher?: User;    
    schoolClass?: {
        id: number;
        name: string;
    };
    schoolSubject?: {
        id: number;
        name: string;
    };
}