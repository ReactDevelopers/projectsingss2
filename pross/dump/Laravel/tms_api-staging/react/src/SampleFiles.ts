import { Key } from 'react';

type  T = {'course': 'course',
    'create_course_run': 'create_course_run',
    'update_course_run': 'update_course_run', 
    'placement': 'placement',
    'placement_result': 'placement_result', 
    'supervisor': 'supervisor',
    'summary': 'summary'
};

//const K: T  =['course','create_course_run', 'update_course_run', 'placement', 'placement_result', 'supervisor']
// type Proxify<T> = {
//     P in keyof T;
// }
const sampleFiles: {[key in keyof T ]: { title: string, name: string} }  = {

    course: {
        title: 'Course Sample File',
        name: 'Courses.xlsx'
    },
    create_course_run: {
        title: 'Create Course Run Sample File',
        name: 'Create_course_runs.xlsx'
    },
    update_course_run: {
        title: 'Update Course Run Sample File',
        name: 'Update_course_runs.xlsx'
    },
    placement: {
        title: 'Placement Sample File',
        name: 'Placement.xlsx'
    },
    placement_result: {
        title: 'Placement Result Sample File',
        name: 'Placement_reports.xlsx'
    },
    supervisor: {
        title: 'Supervisor data Sample File',
        name: 'Supervisors.xlsx'
    },
    summary: {
        title: 'Course Run Summary Sample File',
        name: 'Course_run_summary.xlsx'
    }
}

export default sampleFiles;