import { router, useForm, Link, Head } from "@inertiajs/react";
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
import { ArrowLeftSquareIcon, X } from "lucide-react";
import AttendanceRecord from "@/models/AttendanceRecord";
import Field from "./partials/Field";
import { toast } from "sonner"
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Attendance Record',
    href: route('attendance-record.index'),
  },
  {
    title: 'Create',
    href: route('attendance-record.create'),
  },
];

interface User {
  id: number;
  name: string;
}

interface Student {
  id: number;
  user: User;
  grade: string;
}

interface Teacher {
  id: number;
  name: string;
}

interface Props {
  attendanceRecord: AttendanceRecord;
  classes: Array<{ id: number; name: string }>;
  subjects: Array<{ id: number; name: string }>;
}

export default function Edit({ attendanceRecord, classes, subjects }: Props) {
  const [searchTerm, setSearchTerm] = useState("");
  const [searchStudentResults, setSearchStudentResults] = useState<Student[]>([]);
  const [selectedStudent, setSelectedStudent] = useState<Student | null>(null);
  const [teacherSearchTerm, setTeacherSearchTerm] = useState("");
  const [searchTeacherResults, setSearchTeacherResults] = useState<Array<{ id: number, name: string }>>([]);
  const [selectedTeacher, setSelectedTeacher] = useState<User | null>(null);
  const [isStudentPopoverOpen, setIsStudentPopoverOpen] = useState(false);
  const [isTeacherPopoverOpen, setIsTeacherPopoverOpen] = useState(false);

  const { data, setData, post, processing, errors } = useForm({
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

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route("attendance-record.store"));

    if (errors) {
      console.log(errors);
    } else {
      toast("Attendance record has been created successfully.");
      router.visit(route("attendance-record.index"));
    }
  };

  const handleChangeAttendanceRecord = (attendanceRecord: AttendanceRecord) => {
    setData(attendanceRecord);
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Create Attendance Record" />

      <Card className="max-w-2xl mx-auto">
        <CardHeader>
          <CardTitle>Create Attendance Record</CardTitle>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid auto-rows-min gap-4 md:grid-cols-2">
              <Field attendanceRecord={attendanceRecord} classes={classes} subjects={subjects} onChangeAttendanceRecord={handleChangeAttendanceRecord} errors={errors} />
            </div>
            
            <div className="flex justify-end" >
              <Button type="submit" disabled={processing}>
                Save
              </Button>
              <Button variant="outline" asChild>
                <Link href={route("attendance-record.index")} className="ml-2 flex">
                  <ArrowLeftSquareIcon /> Back
                </Link>
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </AppLayout>
  );
}
