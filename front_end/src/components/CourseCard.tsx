import React from 'react';
import { Course } from '../types/api.types';

interface Props {
  course: Course;
}

const CourseCard: React.FC<Props> = ({ course }) => {
  return (
    <div className="course-card">
      <img src={course.image_preview} alt={course.title} />
      <h3>{course.title}</h3>
      <p>{course.description}</p>
    </div>
  );
};

export default CourseCard;