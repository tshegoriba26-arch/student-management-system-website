@echo off
echo Setting up React Student Management System...

cd C:\xampp\htdocs\student-management-system

echo Creating folder structure...
mkdir react-app\public 2>nul
mkdir react-app\src\components 2>nul

echo Creating package.json...
(
echo {
echo   "name": "student-management-react",
echo   "version": "1.0.0",
echo   "description": "React frontend for Student Management System",
echo   "main": "src/index.js",
echo   "scripts": {
echo     "start": "react-scripts start",
echo     "build": "react-scripts build",
echo     "test": "react-scripts test",
echo     "eject": "react-scripts eject",
echo     "server": "node server.js"
echo   },
echo   "dependencies": {
echo     "react": "^18.2.0",
echo     "react-dom": "^18.2.0",
echo     "react-scripts": "5.0.1",
echo     "react-router-dom": "^6.8.0",
echo     "axios": "^1.3.0",
echo     "materialize-css": "^1.0.0",
echo     "express": "^4.18.0",
echo     "cors": "^2.8.5"
echo   },
echo   "browserslist": {
echo     "production": [
echo       ">0.2%%",
echo       "not dead",
echo       "not op_mini all"
echo     ],
echo     "development": [
echo       "last 1 chrome version",
echo       "last 1 firefox version",
echo       "last 1 safari version"
echo     ]
echo   }
echo }
) > react-app\package.json

echo Setup complete!
echo.
echo Now run these commands:
echo cd react-app
echo npm install
echo npm start

pause