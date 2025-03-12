import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, useForm, router } from '@inertiajs/react';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { X } from 'lucide-react';
import { useState } from 'react';
import User from '@/models/User';
import Student from '@/models/Student';
import axios from 'axios';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
  } from "chart.js";
  import { Bar, Line } from "react-chartjs-2";
  import { ChartData, Point } from "chart.js";  

// Register the Required Components
ChartJS.register(
    CategoryScale, // Required for bar/line charts with categories
    LinearScale,   // Y-axis scaling
    BarElement,    // Bar chart rendering
    PointElement,  // Line chart points
    LineElement,   // Line chart rendering
    Title,         // Chart titles
    Tooltip,       // Tooltip on hover
    Legend         // Chart legend
);

export default function Dashboard() {

    const { props } = usePage(); // Get the full page props

    const { 
        userStudent,    
        latePerMonthChartData, 
        latePerSchoolSubjectChartData,
    } = props as Partial<{
        userStudent: User;
        latePerMonthChartData: ChartData<"line", (number | Point | null)[], unknown>;
        latePerSchoolSubjectChartData: ChartData<"bar", (number | [number, number] | null)[], unknown>;
    }>;
        
    const [searchUserStudentTerm, setSearchUserStudentTerm] = useState("");
    const [searchStudentResults, setSearchStudentResults] = useState<Student[]>([]);
    const [selectedUserStudent, setSelectedUserStudent] = useState<User | null>(userStudent ?? null);
    const [isUserStudentPopoverOpen, setIsUserStudentPopoverOpen] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        id: selectedUserStudent?.id,
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
            setSearchStudentResults(data.data);
        }
    };

    // Add URL update function
    const refreshPageData = (userId: number | null) => {
        router.get(route("dashboard"), {
            user_id: userId
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col justify-end gap-4 rounded-xl p-4">
                <div className="flex items-end gap-2 mr-5 w-[20rem]">
                    <Popover open={isUserStudentPopoverOpen} onOpenChange={setIsUserStudentPopoverOpen}>
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
                                    {searchStudentResults.map((userStudent) => (
                                        <Button
                                            key={userStudent.id}
                                            variant="ghost"
                                            className="w-full justify-start"
                                            onClick={(e) => {
                                                setSelectedUserStudent(userStudent);
                                                setIsUserStudentPopoverOpen(false);
                                                refreshPageData(userStudent.id);
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
                                refreshPageData(null);
                            }}
                        >
                            <X className="w-4 h-4 text-gray-600" />
                        </button>
                    )}
                </div>
            </div>
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <div className="border-sidebar-border/70 dark:border-sidebar-border relative rounded-xl border">
                        {/* Line Chart Container */}
                        <div className="bg-white p-6 rounded-lg shadow-md mx-0">
                            <h3 className="text-lg font-semibold text-gray-700 mb-3">Jumlah Pasien</h3>
                            <div className="h-[400px]">
                                {latePerMonthChartData ? (
                                <Line data={latePerMonthChartData} options={{ 
                                    responsive: true, 
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }} />
                                ) : (
                                <p>Loading chart...</p>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className="border-sidebar-border/70 dark:border-sidebar-border relative rounded-xl border">
                        {/* Bar Chart Container */}
                        <div className="bg-white p-6 rounded-lg shadow-md mx-0">
                            <h3 className="text-lg font-semibold text-gray-700 mb-3">Late Attendance by Subject</h3>
                            <div className="h-[400px]">
                                {latePerSchoolSubjectChartData ? (
                                <Bar data={latePerSchoolSubjectChartData} options={{ 
                                    responsive: true, 
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }} />
                                ) : (
                                <p>Loading chart...</p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div className="border-sidebar-border/70 dark:border-sidebar-border relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border md:min-h-min">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </AppLayout>
    );
}
