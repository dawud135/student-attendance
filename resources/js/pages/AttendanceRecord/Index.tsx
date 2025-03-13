import { useEffect, useState } from 'react'
import { Head } from '@inertiajs/react'
import { Link } from '@inertiajs/react'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { PlusIcon, PencilIcon, ArrowUpIcon, ArrowDownIcon } from 'lucide-react'
import {
  Pagination,
  PaginationContent,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination"
import { router } from '@inertiajs/react'
import { Input } from '@headlessui/react'
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Attendance Record',
    href: route('attendance-record.index'),
  },
];

interface Column {
  name: string
  title: string
  data: string
}

interface AttendanceRecord {
  id: number;
  [key: string]: any; // for other dynamic fields
}

interface AttendanceRecordProps {
  columns: Column[]
}

export default function Index({ columns }: AttendanceRecordProps) {
  const [records, setRecords] = useState<AttendanceRecord[]>([])
  const [currentPage, setCurrentPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)
  const [loading, setLoading] = useState(false)
  const [search, setSearch] = useState('')
  const perPage = 10
  const [order, setOrder] = useState<{ column: number, direction: 'asc' | 'desc' } | null>(null)

  // Fetch attendance records on mount or when page changes
  useEffect(() => {
    const fetchRecords = async () => {
      setLoading(true)
      try {
        const response = await fetch(route('attendance-record.dt', {
          page: currentPage,
          per_page: perPage,
          search: {
            value: search,
            regex: false
          },
          order: order ? [
            {
              column: order.column,
              dir: order.direction
            }
          ] : null
        }), {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
          },
          credentials: 'include'
        })
        const data = await response.json()
        setRecords(data.data)
        setTotalPages(Math.ceil(data.total / perPage))
      } catch (error) {
        console.error('Error fetching records:', error)
      }
      setLoading(false)
    }
    fetchRecords()
  }, [currentPage, search, order])

  const handlePageChange = (page: number) => {
    setCurrentPage(page)
  }

  const handleSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
    setSearch(e.target.value)
  }

  const handleOrder = (column: Column) => {
    let idx = columns.findIndex(c => c.data === column.data);
    setOrder({
      column: idx,
      direction: order?.column === idx && order?.direction === 'asc' ? 'desc' : 'asc'
    })

  }

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Attendance Records" />

      <div className="p-6">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold">Attendance Records</h1>
          <Button asChild>
            <Link href={route('attendance-record.create')}>
              <PlusIcon className="w-4 h-4 mr-2" />
              Add Record
            </Link>
          </Button>
        </div>

        <div className="flex justify-end items-center mb-6">
          <Input
            onChange={handleSearch}
            className="w-1/2 p-2 rounded-md border border-gray-300" />
        </div>

        <Table>
          <TableHeader>
            <TableRow>
              {columns.map((column, idx) => (
                <TableHead
                  key={column.name}
                  className="cursor-pointer"
                  onClick={() => handleOrder(column)}
                >
                  <div className="flex items-center gap-2">
                    {column.title}
                    {order?.column === idx && (
                      order?.direction === 'asc' ? (
                        <ArrowUpIcon className="w-4 h-4" />
                      ) : (
                        <ArrowDownIcon className="w-4 h-4" />
                      )
                    )}
                  </div>
                </TableHead>
              ))}
              <TableHead>Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {loading ? (
              <TableRow>
                <TableCell colSpan={columns.length + 1} className="text-center">
                  Loading...
                </TableCell>
              </TableRow>
            ) : records.length === 0 ? (
              <TableRow>
                <TableCell colSpan={columns.length + 1} className="text-center">
                  No records found
                </TableCell>
              </TableRow>
            ) : (
              records.map((record, i) => (
                <TableRow key={i}>
                  {columns.map((column) => (
                    <TableCell key={column.name}>
                      {record[column.data]}
                    </TableCell>
                  ))}
                  <TableCell>
                    <Link href={route('attendance-record.edit', record.id)} className="text-blue-600 hover:text-blue-800">
                      <PencilIcon className="w-4 h-4" />
                    </Link>
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>

        <div className="mt-4 flex justify-center">
          <Pagination>
            <PaginationContent>
              <PaginationItem>
                <PaginationPrevious
                  onClick={() => handlePageChange(currentPage - 1)}
                  isActive={currentPage === 1}
                  size="sm"
                />
              </PaginationItem>
              {[...Array(totalPages)].map((_, index) => (
                <PaginationItem key={index}>
                  <PaginationLink
                    onClick={() => handlePageChange(index + 1)}
                    isActive={currentPage === index + 1}
                    size="sm"
                  >
                    {index + 1}
                  </PaginationLink>
                </PaginationItem>
              ))}
              <PaginationItem>
                <PaginationNext
                  onClick={() => handlePageChange(currentPage + 1)}
                  isActive={currentPage <= totalPages}
                  size="sm"
                />
              </PaginationItem>
            </PaginationContent>
          </Pagination>
        </div>
      </div>
    </AppLayout>
  )
}