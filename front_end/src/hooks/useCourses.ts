import { useEffect, useState } from 'react';
import axios from 'axios';
import { Course } from '../types/api.types';

const API_URL = 'http://api.cc.localhost/courses';

export const useCourses = (categoryId?: string) => {
  const [courses, setCourses] = useState<Course[]>([]);

  useEffect(() => {
    const url = categoryId ? `${API_URL}?category_id=${categoryId}` : API_URL;
    axios.get<Course[]>(url).then((response) => {
      setCourses(response.data);
    });
  }, [categoryId]);

  return courses;
};