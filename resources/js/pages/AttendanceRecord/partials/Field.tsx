import { useForm } from "@inertiajs/react";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { useDebounce } from "@/hooks/useDebounce";
import axios from "axios";
import { X } from "lucide-react";
import AttendanceRecord from "@/models/AttendanceRecord";
import Student from "@/models/Student";
import User from "@/models/User";

interface Props {
    attendanceRecord: AttendanceRecord;
    classes: Array<{ id: number; name: string }>;
    subjects: Array<{ id: number; name: string }>;
    onChangeAttendanceRecord?: (attendanceRecord: {
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
    }) => void;
}

export default function Field({ attendanceRecord, classes, subjects, onChangeAttendanceRecord }: Props) {
    const [searchUserStudentTerm, setSearchUserStudentTerm] = useState("");
    const [searchUserStudentResults, setSearchUserStudentResults] = useState<User[]>([]);
    const [selectedUserStudent, setSelectedUserStudent] = useState<User | null>(
        attendanceRecord.user ? attendanceRecord.user : null
    );
    const [teacherSearchUserStudentTerm, setTeacherSearchUserStudentTerm] = useState("");
    const [searchTeacherResults, setSearchTeacherResults] = useState<Array<{ id: number, name: string }>>([]);
    const [selectedTeacher, setSelectedTeacher] = useState<User | null>(
        attendanceRecord.teacher ? attendanceRecord.teacher : null
    );
    const [isStudentPopoverOpen, setIsStudentPopoverOpen] = useState(false);
    const [isTeacherPopoverOpen, setIsTeacherPopoverOpen] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        id: attendanceRecord.id,
        user_id: attendanceRecord.user_id,
        teacher_id: attendanceRecord.teacher_id,
        school_class_id: attendanceRecord.school_class_id,
        school_subject_id: attendanceRecord.school_subject_id,
        grade: attendanceRecord.grade,
        date: attendanceRecord.date,
        status: attendanceRecord.status,
        minutes_late: attendanceRecord.minutes_late,
        reason: attendanceRecord.reason,
    });

    const handleUserSearch = async (value: string) => {
        setSearchUserStudentTerm(value);
        if (value.length >= 0) {
            const { data } = await axios.post(route("user.search"), {
                search: {
                    value: value,
                    regex: false
                },
                role: "student"
            });
            setSearchUserStudentResults(data.data);
        }
    };

    const handleTeacherSearch = async (value: string) => {
        setTeacherSearchUserStudentTerm(value);
        if (value.length >= 0) {
            const { data } = await axios.post(route("user.search"), {
                search: {
                    value: value,
                    regex: false,
                    
                },
                role: "teacher"
            });
            setSearchTeacherResults(data.data);
        }
    };

    const updateData = (
        field: "user_id" | "teacher_id" | "school_class_id" | "school_subject_id" | "grade" | "date" | "status" | "minutes_late" | "reason",
        value: any
    ) => {
        setData(field, value);
        
        if (onChangeAttendanceRecord) {
            onChangeAttendanceRecord({
                ...data,
                [field]: value
            });
        }
    };

    return (
        <>
            <div className="space-y-2">
                <Label>Student</Label>
                <div className="flex items-center gap-2 mr-5">
                    <Popover open={isStudentPopoverOpen} onOpenChange={setIsStudentPopoverOpen}>
                        <PopoverTrigger asChild>
                            <Button variant="outline" className="w-full justify-start">
                                {selectedUserStudent ? selectedUserStudent.name : "Select student..."}
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent className="w-80">
                            <div className="space-y-2">
                                <Input
                                    type="search"
                                    placeholder="Search students..."
                                    value={searchUserStudentTerm}
                                    onChange={(e) => handleUserSearch(e.target.value)}
                                />
                                <div className="max-h-48 overflow-auto">
                                    {searchUserStudentResults.map((userStudent) => (
                                        <Button
                                            key={userStudent.id}
                                            variant="ghost"
                                            className="w-full justify-start"
                                            onClick={(e) => {
                                                setSelectedUserStudent(userStudent);
                                                updateData("grade", userStudent.student?.grade);
                                                updateData("user_id", userStudent.id);
                                                setIsStudentPopoverOpen(false);
                                            }}
                                        >
                                            {userStudent.name}
                                        </Button>
                                    ))}
                                </div>
                            </div>
                        </PopoverContent>
                    </Popover>
                    {/* Clear Selection Button */}
                    {selectedUserStudent && (
                        <button
                            type="button"
                            className="p-2 rounded-full bg-gray-200 hover:bg-gray-300 transition"
                            onClick={() => {
                                setSelectedUserStudent(null);
                                updateData("grade", "");
                                updateData("user_id", 0);
                            }}
                        >
                            <X className="w-4 h-4 text-gray-600" />
                        </button>
                    )}
                </div>
                {errors.user_id && <p className="text-red-500 text-sm">{errors.user_id}</p>}
            </div>

            <div className="space-y-2">
                <Label>Teacher</Label>
                <div className="flex items-center gap-2 mr-5">
                    <Popover open={isTeacherPopoverOpen} onOpenChange={setIsTeacherPopoverOpen}>
                        <PopoverTrigger asChild>
                            <Button variant="outline" className="w-full justify-start">
                                {selectedTeacher ? selectedTeacher.name : "Select teacher..."}
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent className="w-80">
                            <div className="space-y-2">
                                <Input
                                    type="search"
                                    placeholder="Search teachers..."
                                    value={teacherSearchUserStudentTerm}
                                    onChange={(e) => handleTeacherSearch(e.target.value)}
                                />
                                <div className="max-h-48 overflow-auto">
                                    {searchTeacherResults.map((teacher) => (
                                        <Button
                                            key={teacher.id}
                                            variant="ghost"
                                            className="w-full justify-start"
                                            onClick={() => {
                                                setSelectedTeacher(teacher)
                                                setTeacherSearchUserStudentTerm(teacher.name);
                                                updateData("teacher_id", teacher.id);
                                                setIsTeacherPopoverOpen(false);
                                            }}
                                        >
                                            {teacher.name}
                                        </Button>
                                    ))}
                                </div>
                            </div>
                        </PopoverContent>
                    </Popover>

                    {/* Clear Selection Button */}
                    {selectedTeacher && (
                        <button
                            type="button"
                            className="p-2 rounded-full bg-gray-200 hover:bg-gray-300 transition"
                            onClick={() => {
                                setSelectedTeacher(null);
                                updateData("teacher_id", 0);
                            }}
                        >
                            <X className="w-4 h-4 text-gray-600" />
                        </button>
                    )}
                </div>
                {errors.teacher_id && <p className="text-red-500 text-sm">{errors.teacher_id}</p>}
            </div>

            <div className="space-y-2">
                <Label>Class</Label>
                <Select
                    value={data.school_class_id ? data.school_class_id.toString() : ""}
                    onValueChange={(value) => updateData("school_class_id", parseInt(value))}
                >
                    <SelectTrigger>
                        <SelectValue placeholder="Select class" />
                    </SelectTrigger>
                    <SelectContent>
                        {classes.map((class_) => (
                            <SelectItem key={class_.id} value={class_.id.toString()}>
                                {class_.name}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                {errors.school_class_id && <p className="text-red-500 text-sm">{errors.school_class_id}</p>}
            </div>

            <div className="space-y-2">
                <Label>Subject</Label>
                <Select
                    value={data.school_subject_id ? data.school_subject_id.toString() : ""}
                    onValueChange={(value) => updateData("school_subject_id", parseInt(value))}
                >
                    <SelectTrigger>
                        <SelectValue placeholder="Select subject" />
                    </SelectTrigger>
                    <SelectContent>
                        {subjects.map((subject) => (
                            <SelectItem key={subject.id} value={subject.id.toString()}>
                                {subject.name}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                {errors.school_subject_id && <p className="text-red-500 text-sm">{errors.school_subject_id}</p>}
            </div>

            <div className="space-y-2">
                <Label>Grade</Label>
                <Input
                    type="text"
                    value={data.grade}
                    onChange={(e) => updateData("grade", e.target.value)}
                />
                {errors.grade && <p className="text-red-500 text-sm">{errors.grade}</p>}
            </div>

            <div className="space-y-2">
                <Label>Date</Label>
                <Input
                    type="date"
                    value={data.date}
                    onChange={(e) => updateData("date", e.target.value)}
                />
                {errors.date && <p className="text-red-500 text-sm">{errors.date}</p>}
            </div>

            <div className="space-y-2">
                <Label>Status</Label>
                <Select
                    value={data.status}
                    onValueChange={(value) => updateData("status", value)}
                >
                    <SelectTrigger>
                        <SelectValue placeholder="Select status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="present">Present</SelectItem>
                        <SelectItem value="absent">Absent</SelectItem>
                        <SelectItem value="late">Late</SelectItem>
                    </SelectContent>
                </Select>
                {errors.status && <p className="text-red-500 text-sm">{errors.status}</p>}
            </div>

            <div className="space-y-2">
                <Label>Minutes Late</Label>
                <Input
                    type="number"
                    value={data.minutes_late}
                    onChange={(e) => updateData("minutes_late", parseInt(e.target.value))}
                />
                {errors.minutes_late && <p className="text-red-500 text-sm">{errors.minutes_late}</p>}
            </div>

            <div className="space-y-2">
                <Label>Reason</Label>
                <Input
                    type="text"
                    value={data.reason}
                    onChange={(e) => updateData("reason", e.target.value)}
                />
                {errors.reason && <p className="text-red-500 text-sm">{errors.reason}</p>}
            </div>
        </>
    );
}