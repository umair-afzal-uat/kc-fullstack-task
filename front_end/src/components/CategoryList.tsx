import React from 'react';
import { Category } from '../types/api.types';

interface Props {
  categories: Category[];
  onSelect: (id: string | null) => void;
}

const CategoryList: React.FC<Props> = ({ categories, onSelect }) => {
  return (
    <ul>
      <li onClick={() => onSelect(null)}>All Categories</li>
      {categories.map((category) => (
        <li key={category.id} onClick={() => onSelect(category.id)}>
          {category.name} ({category.count_of_courses})
        </li>
      ))}
    </ul>
  );
};

export default CategoryList;