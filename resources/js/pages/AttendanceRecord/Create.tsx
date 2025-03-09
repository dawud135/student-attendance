import { useState } from 'react'
import { Head } from '@inertiajs/react'
import { format } from 'date-fns'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Calendar } from '@/components/ui/calendar'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'

interface Student {
  id: number
  name: string
  grade: string
}

interface AttendanceRecordProps {
  students: Student[]
  classes: Array<{ id: number; name: string }>
  subjects: Array<{ id: number; name: string }>
}

export default function Index({ students, classes, subjects }: AttendanceRecordProps) {
  const [selectedDate, setSelectedDate] = useState<Date>(new Date())
  const [selectedClass, setSelectedClass] = useState('')
  const [selectedSubject, setSelectedSubject] = useState('')
  const [attendanceData, setAttendanceData] = useState<Record<number, {
    status: 'on-time' | 'late' | 'absent'
    minutesLate: number
    reason: string
  }>>({})

  const handleAttendanceChange = (studentId: number, field: string, value: any) => {
    setAttendanceData(prev => ({
      ...prev,
      [studentId]: {
        ...prev[studentId],
        [field]: value
      }
    }))
  }

  const handleSubmit = () => {
    // TODO: Submit attendance data to backend
  }

  return (
    <>
      <Head title="Record Attendance" />

      <div className="p-6">
        <h1 className="text-2xl font-bold mb-6">Record Attendance</h1>

        <div className="flex gap-4 mb-6">
          <Popover>
            <PopoverTrigger asChild>
              <Button variant="outline">
                {selectedDate ? format(selectedDate, 'PPP') : 'Pick a date'}
              </Button>
            </PopoverTrigger>
            <PopoverContent className="w-auto p-0">
              <Calendar
                mode="single"
                selected={selectedDate}
                onSelect={(day) => setSelectedDate(day as Date)}
                initialFocus
              />
            </PopoverContent>
          </Popover>

          <Select value={selectedClass} onValueChange={setSelectedClass}>
            <SelectTrigger className="w-[200px]">
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

          <Select value={selectedSubject} onValueChange={setSelectedSubject}>
            <SelectTrigger className="w-[200px]">
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
        </div>

        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Student Name</TableHead>
              <TableHead>Grade</TableHead>
              <TableHead>Status</TableHead>
              <TableHead>Minutes Late</TableHead>
              <TableHead>Reason</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {students.map((student) => (
              <TableRow key={student.id}>
                <TableCell>{student.name}</TableCell>
                <TableCell>{student.grade}</TableCell>
                <TableCell>
                  <Select
                    value={attendanceData[student.id]?.status || 'on-time'}
                    onValueChange={(value) => handleAttendanceChange(student.id, 'status', value)}
                  >
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="on-time">On Time</SelectItem>
                      <SelectItem value="late">Late</SelectItem>
                      <SelectItem value="absent">Absent</SelectItem>
                    </SelectContent>
                  </Select>
                </TableCell>
                <TableCell>
                  <Input
                    type="number"
                    min="0"
                    value={attendanceData[student.id]?.minutesLate || 0}
                    onChange={(e) => handleAttendanceChange(student.id, 'minutesLate', parseInt(e.target.value))}
                    disabled={attendanceData[student.id]?.status !== 'late'}
                    className="w-24"
                  />
                </TableCell>
                <TableCell>
                  <Input
                    value={attendanceData[student.id]?.reason || ''}
                    onChange={(e) => handleAttendanceChange(student.id, 'reason', e.target.value)}
                    disabled={attendanceData[student.id]?.status === 'on-time'}
                  />
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>

        <div className="mt-6">
          <Button onClick={handleSubmit}>Save Attendance</Button>
        </div>
      </div>
    </>
  )
} 