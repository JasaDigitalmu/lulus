# lulus
Aplikasi Lulus ini dibuat untuk mempermudah sekolah dalam pengelolaan penilaian transkip nilai, transkip nilai mulai dari semester 1 hingga semester 6 dan nilai ujian sekolah

**1. Installation & Setup**

The application is built with PHP Native and uses Composer for dependencies.

	Root Directory: c:/xampp/htdocs/lulus2/
	Database: lulus2 (Imported via database.sql)
	Dependencies: installed via composer install

**2. Access Information**
   
**Public Portal (Student)**
	
	URL: http://localhost/lulus2/
	Features: Search NISN, View Status (LULUS/TIDAK), Download SKL/Transkrip PDF.
	
**Admin Panel**
	
	URL: http://localhost/lulus2/login.php
	Default Credentials:
	Username: admin
	Password: password

**3. Security Features**

CSRF Protection: All forms are protected against Cross-Site Request Forgery. Uses helpers/csrf.php.
SQL Injection: Prevented using PDO Prepared Statements.
XSS: Output escaping using htmlspecialchars.

**4. Admin Workflow**

Follow these steps to set up the announcement:

**Step 1: School Identity**

	Go to Pengaturan Sekolah on the sidebar.
	Update School Name, Headmaster Name/NIP, Graduation Date, and Letter Number.
	Upload Logo: The logo will appear on the Login page, Student Portal, and PDF Header.

**Step 2: Manage Classes (New)**
		
		Go to Data Kelas.
		Add classes (e.g., 12-F1 to 12-F8).
		You can Edit or Delete classes as needed.

**Step 3: Manage Subjects**

	Go to Mata Pelajaran.
	Add or Edit subjects.
	Assign Classes: You can assign a subject to "Semua Kelas" or select multiple specific classes (e.g., "12-F1", "12-F3").
	New Feature: Click the "Pilih Kelas..." dropdown to see a list of checkboxes. Simply check the classes you want.
	Ensure all subjects are categorized correctly (Umum, Pilihan, Muatan Lokal).
	Note on Navigation
	Sidebar Toggle: On smaller screens or to save space, click the "Menu" button at the top left to hide/show the sidebar.

**Step 4: Manage Students**

	Go to Data Siswa.
	Import Excel: Upload a .xlsx file.
	Format Template: NISN | NAMA LENGKAP | KELAS | JENIS KELAMIN (L/P) | TEMPAT LAHIR | TANGGAL LAHIR (YYYY-MM-DD) | STATUS (LULUS/TIDAK LULUS)

**Step 5: Input Grades (Per Subject Import)**
	
	Go to Nilai Siswa.
	Click Import Nilai.
		Step 1: Select Subject: Choose the subject.
		Step 2: Select Class (Optional): Choose a class to download a pre-filled template for ONLY that class.
		Step 3: Download Template: Click the button to get the Excel file.
		Columns: NISN, NAMA SISWA, SEM 1...SEM 6, UJIAN SEKOLAH.
		Step 4: Upload: Fill and upload.
		System Calculation: Final Score = (Average(Sem1-6) + UjianSekolah) / 2.
		Printing: You can click the "Cetak" dropdown on the list to choose between A4 and Legal paper sizes.
		
**5. Student Flow**

	Open the Homepage.
	Enter NISN (e.g., 0067221748 from sample data).
	Click "Cek Kelulusan".
	If LULUS:
		Select Paper Size (A4 or Legal) from the dropdown.
		Click "Cetak Surat Keterangan Lulus (SKL)".


**_Bila ada yang ingin berdonasi seikhlasnya silahkan ke rekening BCA 5491140110 a/n Ahmad Miftahudin_**

**Semoga Aplikasi Ini sedikit membantu bapak/ibu disekolahn, Jazakumullah Khairan**
