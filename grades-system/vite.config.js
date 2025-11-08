import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            input: ['resources/css/default.css', 'resources/js/app.js'],
            input: ['resources/css/course.css', 'resources/js/app.js'],
            input: ['resources/css/my_class.css', 'resources/js/app.js'],
            input: ['resources/css/classes.css', 'resources/js/app.js'],
            input: ['resources/css/classes_view.css', 'resources/js/app.js'],
            input: ['resources/css/login.css', 'resources/js/app.js'],
            input: ['resources/css/grading&score.css', 'resources/js/app.js'],
            input: ['resources/css/my_grades.css', 'resources/js/app.js'],
            input: ['resources/css/my_class_archive.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
