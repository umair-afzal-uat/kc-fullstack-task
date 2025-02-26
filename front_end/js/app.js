// DOM Elements
const categoryList = document.getElementById('category-list');
const courseList = document.getElementById('course-list');

// API Endpoints
const CATEGORIES_API = 'http://api.cc.localhost/categories';
const COURSES_API = 'http://api.cc.localhost/courses';

// State
let allCategories = [];
let allCourses = [];

// Fetch Categories
async function fetchCategories() {
  try {
    const response = await fetch(CATEGORIES_API);
    const data = await response.json();
    allCategories = data;
    renderCategories();
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
}

// Fetch Courses
async function fetchCourses(categoryId = null) {
  try {
    const url = categoryId ? `${COURSES_API}?category_id=${categoryId}` : COURSES_API;
    const response = await fetch(url);
    const data = await response.json();
    allCourses = data;
    renderCourses();
  } catch (error) {
    console.error('Error fetching courses:', error);
  }
}

// Render Categories
function renderCategories() {
  categoryList.innerHTML = `
    <li onclick="filterCourses(null)">All Categories</li>
    ${allCategories
      .map(
        (category) => `
          <li onclick="filterCourses('${category.id}')">
            ${category.name} (${category.count_of_courses})
          </li>
        `
      )
      .join('')}
  `;
}

// Render Courses
function renderCourses() {
  if (allCourses.length === 0) {
    courseList.innerHTML = '<p>No courses available.</p>';
    return;
  }

  courseList.innerHTML = allCourses
    .map(
      (course) => `
        <div class="course-card">
          <img src="${course.image_preview}" alt="${course.title}" />
          <h3>${course.title}</h3>
          <p>${course.description}</p>
        </div>
      `
    )
    .join('');
}

// Filter Courses by Category
function filterCourses(categoryId) {
  fetchCourses(categoryId);
}

// Initialize App
function initApp() {
  fetchCategories();
  fetchCourses();
}

// Start the Application
initApp();