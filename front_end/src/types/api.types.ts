export interface Category {
    id: string;
    name: string;
    parent_id: string | null;
    count_of_courses: number;
  }
  
  export interface Course {
    course_id: string;
    title: string;
    description: string;
    image_preview: string;
    category_id: string;
  }