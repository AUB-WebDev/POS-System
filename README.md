
# POS System🖥️💵

A Point Of Sale system for small and medium businesses built using PHP and AdminLTE template for educational purpose.



## Features
Here's a brief summary of the features:

- **Product Management**
    
        Add, edit, remove products with categories, prices, stock and more. 
 
- **Transaction Management**

        Scan items, apply discounts, generate printable receipts and KHQR.

- **User Roles**
     
    - Admin (full access)
    - User (limited to sales).

- **Inventory Tracking**

- **Calculate Tax**

- **Print Barcode**
- **and more.**
## App Interface
![App Screenshot](https://github.com/VattanacKong/POS-System/blob/main/screenshots/Screenshot%202025-06-23%20192804.png?raw=true)

![App Screenshot](https://github.com/VattanacKong/POS-System/blob/main/screenshots/Screenshot%202025-06-23%20192835.png?raw=true)

![App Screenshot](https://github.com/VattanacKong/POS-System/blob/main/screenshots/Screenshot%202025-06-23%20192910.png?raw=true)

[More Screenshots](https://github.com/VattanacKong/POS-System/tree/main/screenshots)
## Tech Stack

Frontend: HTML5, CSS3, JavaScript (Vanilla, Ajax and jQuery)

Backend: PHP

Database: MySQL 

Dependencies: Bootstrap, dotenv, php_barcode, fpdf and more


## Installation

**Prerequisites**
- PHP 8.x
- [Composer](https://getcomposer.org/download/)
- [Git](https://git-scm.com/downloads)
- [Xampp](https://www.apachefriends.org/download.html) or any other app of your choice

1. Clone the project:
```bash
git clone https://github.com/VattanacKong/POS-System.git
```
2. Go to POS-System directory:
```bash
cd POS-System
```
3. Install Dependencies:
```bash
composer install
```
4. Create Database:
Import the create_database.sql into your MySQL database. [Tutorial](https://youtu.be/q0EBUXTQQRY)

5. Create .env File:
Create a **.env** file in the current directory. Paste everything from **.env.example** and change it to your credentials. **You must get Gmail SMTP password if you want to use the forgot password feature.** [Tutorial](https://youtu.be/I9x0w8cjR_o)

**NOTE**: You can leave [SERVICE_URI, SSL_MODE, CA_CERTIFICATE] as it is.
## Usage/Examples

1. Access your app. If you use Xampp:
        
- Go to http://localhost:80/your_project_dir/POS-System

2. Enter the credentials below:
- Email: admin@gmail.com
- Password: 123 
3. Create a new user and change the admin password. (Optional)
## Authors

- [@VattanacKong](https://github.com/VattanacKong)


## Contributing

Contributions are always welcome!

1. Fork the repository.

2. Create a new branch:
``` 
git checkout -b feature/your-feature-name
``` 
3. Make your changes and commit them:
```
git commit -m "Add your feature"
```
4. Push to your branch:
```
git push origin feature/your-feature-name
```
5. Open a pull request on GitHub.

## Acknowledgements

 - [AdminLTE](https://github.com/ColorlibHQ/AdminLTE)
 - [AdminLTE README](https://github.com/ColorlibHQ/AdminLTE/blob/master/README.md)


## License

[MIT](https://choosealicense.com/licenses/mit/)

