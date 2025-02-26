import React, { useState } from 'react';
import { useCategories } from './hooks/useCategories';
import { useCourses } from './hooks/useCourses';
import CategoryList from './components/CategoryList';
import CourseList from './components/CourseList';

const App: React.FC = () => {
  const [selectedCategoryId, setSelectedCategoryId] = useState<string | null>(null);
  const categories = useCategories();
  const courses = useCourses(selectedCategoryId);

  return (
    <div>
      <h1>Course Catalog</h1>
      <div style={{ display: 'flex' }}>
        <div style={{ width: '20%' }}>
          <CategoryList categories={categories} onSelect={setSelectedCategoryId} />
        </div>
        <div style={{ width: '80%' }}>
          <CourseList courses={courses} />
        </div>
      </div>
    </div>
  );
};

export default App;