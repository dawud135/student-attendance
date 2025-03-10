import { Head } from '@inertiajs/react'
import { useState } from 'react'
import { format } from 'date-fns'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Button } from '@/components/ui/button'
import { Calendar } from '@/components/ui/calendar'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem } from '@/components/ui/command'
import axios from 'axios'
import debounce from 'lodash/debounce'

interface Student {
  id: number
  name: string
  grade: string
}

interface AttendanceRecordProps {
  attendanceRecord: {
    id: number
    student_id: number
    school_class_id: number
    school_subject_id: number
    teacher_id: number
    status: string
    grade: number
    late_minutes: number
  }
  classes: Array<{ id: number; name: string }>
  subjects: Array<{ id: number; name: string }>
}

export default function Edit({ attendanceRecord, classes, subjects }: AttendanceRecordProps) {
  const [selectedDate, setSelectedDate] = useState<Date>(new Date())
  const [selectedClass, setSelectedClass] = useState('')
  const [selectedSubject, setSelectedSubject] = useState(attendanceRecord.school_subject_id ? attendanceRecord.school_subject_id.toString() : '')
  const [searchStudentResults, setSearchStudentResults] = useState<Student[]>([])
  const [selectedStudent, setSelectedStudent] = useState<Student | null>(null)
  const [grade, setGrade] = useState(attendanceRecord.grade)
  const [lateMinutes, setLateMinutes] = useState(attendanceRecord.late_minutes)
  const [open, setOpen] = useState(false)

  const searchStudents = debounce(async (searchTerm: string) => {
    if (!searchTerm) {
      setSearchStudentResults([])
      return
    }
    
    try {
      const response = await axios.post(route('student.search'), {
        search: searchTerm,
      })
      setSearchStudentResults(response.data)
    } catch (error) {
      console.error('Error searching students:', error)
      setSearchStudentResults([])
    }
  }, 300)

  const handleSelectStudent = (student: Student) => {
    setSelectedStudent(student)
    setOpen(false)
  }

  const handleSubmit = async () => {
    if (!selectedStudent) return

    try {
      await axios.put(`/attendance-records/${attendanceRecord.id}`, {
        student_id: selectedStudent.id,
        school_class_id: parseInt(selectedClass),
        school_subject_id: parseInt(selectedSubject),
        grade,
        late_minutes: lateMinutes,
      })
      
      window.location.href = '/attendance-records'
    } catch (error) {
      console.error('Error updating attendance record:', error)
    }
  }

  return (
    <>
      <Head title="Edit Attendance Record" />

      <div className="p-6">
        <h1 className="text-2xl font-bold mb-6">Edit Attendance Record</h1>

        <div className="space-y-4">
          <div className="flex gap-4">
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

          <div className="flex flex-col gap-4">
            <Popover open={open} onOpenChange={setOpen}>
              <PopoverTrigger asChild>
                <Button variant="outline" className="w-[200px] justify-start">
                  {selectedStudent ? selectedStudent.name : 'Select student...'}
                </Button>
              </PopoverTrigger>
              <PopoverContent className="w-[200px] p-0">
                <Command>
                  <CommandInput 
                    placeholder="Search student..." 
                    onValueChange={searchStudents}
                  />
                  <CommandEmpty>No students found.</CommandEmpty>
                  <CommandGroup>
                    {searchStudentResults.map((student) => (
                      <CommandItem
                        key={student.id}
                        value={student.name}
                        onSelect={() => handleSelectStudent(student)}
                      >
                        {student.name}
                      </CommandItem>
                    ))}
                  </CommandGroup>
                </Command>
              </PopoverContent>
            </Popover>

            <input
              type="number"
              value={grade}
              onChange={(e) => setGrade(parseInt(e.target.value))}
              placeholder="Grade"
              className="p-2 border rounded"
            />

            <input
              type="number"
              value={lateMinutes}
              onChange={(e) => setLateMinutes(parseInt(e.target.value))}
              placeholder="Late minutes"
              className="p-2 border rounded"
            />

            <Button onClick={handleSubmit} className="w-[200px]">
              Update Record
            </Button>
          </div>
        </div>
      </div>
    </>
  )
}
