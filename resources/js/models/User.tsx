import Student from "./Student";

export default interface User {
    id: number;
    name: string;
    student?: Student;
}