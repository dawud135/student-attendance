import { useState } from 'react'
import { Head, router } from '@inertiajs/react'
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
  classes: Array<{ id: number; name: string }>
  subjects: Array<{ id: number; name: string }>
}

export default function Create({ classes, subjects }: AttendanceRecordProps) {
  const [selectedDate, setSelectedDate] = useState<Date>(new Date())
  const [selectedClass, setSelectedClass] = useState('')
  const [selectedSubject, setSelectedSubject] = useState('')
  const [searchResults, setSearchResults] = useState<Student[]>([])
  const [selectedStudent, setSelectedStudent] = useState<Student | null>(null)
  const [open, setOpen] = useState(false)

  const searchStudents = debounce(async (searchTerm: string) => {
    if (!searchTerm) {
      setSearchResults([])
      return
    }
    
    try {
      const response = await axios.get(`/students/search?term=${searchTerm}`)
      setSearchResults(response.data)
    } catch (error) {
      console.error('Error searching students:', error)
      setSearchResults([])
    }
  }, 300)

  const handleSelectStudent = (student: Student) => {
    if (!selectedStudents.find(s => s.id === student.id)) {
      setSelectedStudents([...selectedStudents, student])
    }
    setOpen(false)
  }

  const handleRemoveStudent = (studentId: number) => {
    setSelectedStudents(selectedStudents.filter(s => s.id !== studentId))
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

        <div className="mb-6">
          <Select
            value={selectedStudent?.id.toString()}
            onValueChange={(value) => {
              const student = searchResults.find(s => s.id.toString() === value);
              setSelectedStudent(student || null);
            }}
          >
            <SelectTrigger className="w-[400px]">
              <SelectValue placeholder="Select student" />
            </SelectTrigger>
            <SelectContent>
              <CommandInput 
                placeholder="Search students..."
                onValueChange={(value) => {
                  router.get(
                    route('api.students.search'),
                    { search: value },
                    {
                      preserveState: true,
                      preserveScroll: true,
                      only: ['searchResults']
                    }
                  );
                }}
              />
              {searchResults.map((student) => (
                <SelectItem key={student.id} value={student.id.toString()}>
                  {student.name} - {student.grade}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>

          {selectedStudent && (
            <div className="mt-4 p-2 border rounded flex items-center justify-between">
              <span>{selectedStudent.name} - {selectedStudent.grade}</span>
            </div>
          )}
        </div>

        <div className="mt-6">
          <Button onClick={handleSubmit}>Save Attendance</Button>
        </div>
      </div>
    </>
  )
} 