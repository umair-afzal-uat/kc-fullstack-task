import { useEffect, useState } from 'react';
import axios from 'axios';
import { Category } from '../types/api.types';

const API_URL = 'http://api.cc.localhost/categories';

export const useCategories = () => {
  const [categories, setCategories] = useState<Category[]>([]);

  useEffect(() => {
    axios.get<Category[]>(API_URL).then((response) => {
      setCategories(response.data);
    });
  }, []);

  return categories;
};