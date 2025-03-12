import User from "./User";

export default interface Student {
    id: number;
    user: User;
    grade: string;
    name: string;
}