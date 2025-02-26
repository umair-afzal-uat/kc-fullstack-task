import React from 'react';
import CourseCard from './CourseCard';
import { Course } from '../types/api.types';

interface Props {
  courses: Course[];
}

const CourseList: React.FC<Props> = ({ courses }) => {
  return (
    <div className="course-list">
      {courses.length > 0 ? (
        courses.map((course) => <CourseCard key={course.course_id} course={course} />)
      ) : (
        <p>No courses available.</p>
      )}
    </div>
  );
};

export default CourseList;