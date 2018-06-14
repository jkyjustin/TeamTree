DROP TABLE Accounts CASCADE CONSTRAINTS;
DROP TABLE Students CASCADE CONSTRAINTS;
DROP TABLE Employers CASCADE CONSTRAINTS;
DROP TABLE Schools CASCADE CONSTRAINTS;
DROP TABLE Courses CASCADE CONSTRAINTS;
DROP TABLE Companies CASCADE CONSTRAINTS;
DROP TABLE Endorsements CASCADE CONSTRAINTS;
DROP TABLE Reviews CASCADE CONSTRAINTS;

CREATE TABLE Schools (
	schoolID INT,
	sName VARCHAR(40),
	campusLoc VARCHAR(100),
	PRIMARY KEY (schoolID)
);

grant select on Schools to public;

CREATE TABLE Accounts (
	acctID INT,
	fname VARCHAR(40),
	lname VARCHAR(40),
	email VARCHAR(40),
	password VARCHAR(40),
	isEmployer NUMBER(1), /* 0 = student acct, 1 = employer acct */
	PRIMARY KEY (acctID),
	CONSTRAINT email_uniq UNIQUE (email)
);

grant select on Accounts to public;

CREATE TABLE Students (
	acctID INT,
	schoolID INT NOT NULL,
	PRIMARY KEY (acctID),
	FOREIGN KEY (acctID) REFERENCES Accounts(acctID)
		ON DELETE CASCADE
		DEFERRABLE INITIALLY DEFERRED, /* aka. ON UPDATE CASCADE */	
	FOREIGN KEY (schoolID) REFERENCES Schools(schoolID)
		ON DELETE CASCADE
		DEFERRABLE INITIALLY DEFERRED
);

grant select on Students to public;

CREATE TABLE Companies (
	companyID INT,
	name VARCHAR(40),
	website VARCHAR(40),
	PRIMARY KEY (companyID)
);

grant select on Companies to public;

CREATE TABLE Employers (
	acctID INT,
	companyID INT NOT NULL,
	PRIMARY KEY (acctID),
	FOREIGN KEY (acctID) REFERENCES Accounts(acctID)
		ON DELETE CASCADE
		DEFERRABLE INITIALLY DEFERRED,	
	FOREIGN KEY (companyID) REFERENCES Companies(companyID)
		ON DELETE CASCADE
		DEFERRABLE INITIALLY DEFERRED
);

grant select on Employers to public;

CREATE TABLE Courses (
	courseNo INT,
	dept CHAR(4),
	schoolID INT,
	PRIMARY KEY (courseNo, dept, schoolID),
	FOREIGN KEY (schoolID) REFERENCES Schools(schoolID)
);

grant select, insert on Courses to public;

CREATE TABLE Endorsements (
	employerID INT,
	studentID INT,
	PRIMARY KEY (employerID, studentID),
	FOREIGN KEY (employerID) REFERENCES Employers(acctID)
		ON DELETE SET NULL
		DEFERRABLE INITIALLY DEFERRED
);

grant select on Endorsements to public;

CREATE TABLE Reviews (
	datetime TIMESTAMP,
	reviewID INT,
	reviewerID INT,
	revieweeID INT,
	courseNo INT,
	dept CHAR(4),
	schoolID INT,
	score INT,
	assignmentDesc VARCHAR(255),
	content VARCHAR(1000),
	numLikes INT,
	numDislikes INT,
	PRIMARY KEY (datetime, reviewID, reviewerID, revieweeID, courseNo, dept),
	FOREIGN KEY (reviewerID) REFERENCES Students(acctID)
		ON DELETE CASCADE
		DEFERRABLE INITIALLY DEFERRED,
	FOREIGN KEY (revieweeID) REFERENCES Students(acctID)
		ON DELETE CASCADE
		DEFERRABLE INITIALLY DEFERRED,
	FOREIGN KEY (courseNo, dept, schoolID) REFERENCES Courses(courseNo, dept, schoolID)
);

grant select on Reviews to public;

/* Schools */
INSERT INTO Schools
VALUES (1, 'University of British Columbia', '2255 Lower Mall, Vancouver, BC, FDS 334');

INSERT INTO Schools
VALUES (2, 'Simon Fraser University', '8888 University Dr, Burnaby, BC V5A 1S6');

/* Companies */
INSERT INTO Companies
VALUES (1, 'Google', 'Google.com');

INSERT INTO Companies
VALUES (2, 'Microsoft', 'Msft.com');

INSERT INTO Companies
VALUES (3, 'Facebook', 'Facebook.com');

/* UBC Students */
INSERT INTO Accounts
VALUES(1, 'Justin', 'Yoon', 'jy@email.com', 'password', 0);
INSERT INTO Students
VALUES (1, 1);

INSERT INTO Accounts
VALUES(8, 'Justin', 'ABC', 'jy123@email.com', 'password', 0);
INSERT INTO Students
VALUES (8, 1);

INSERT INTO Accounts
VALUES(2, 'Blake', 'Turnable', 'blaketmeng@gmail.com', 'password', 0);
INSERT INTO Students
VALUES(2, 1);

/* SFU Students */
INSERT INTO Accounts
VALUES(3, 'LeBron', 'James', 'lb_goat@cavs.com', 'password', 0);
INSERT INTO Students
VALUES(3, 2);

INSERT INTO Accounts
VALUES(4, 'Steph', 'Curry', 'i_suck@yahoo.com', 'password', 0);
INSERT INTO Students
VALUES(4, 2);

/* Employers */
INSERT INTO Accounts
VALUES(5, 'Billy', 'Googley', 'BGoogley@GoogleRecruiting.com', 'password', 1);
INSERT INTO Employers
VALUES (5, 1);

INSERT INTO Accounts
VALUES(6, 'Bill', 'Gates', 'bgates@msft.com', 'password', 1);
INSERT INTO Employers
VALUES (6, 2);

INSERT INTO Accounts
VALUES(7, 'Mark', 'Zuckerberg', 'markz@facebook.com', 'password', 1);
INSERT INTO Employers
VALUES (7, 3);

/* UBC Courses */
INSERT INTO Courses
VALUES (110, 'CPSC', 1);

INSERT INTO Courses
VALUES (121, 'CPSC', 1);

INSERT INTO Courses
VALUES (304, 'CPSC', 1);

INSERT INTO Courses
VALUES (317, 'CPSC', 1);

/* SFU Courses */
INSERT INTO Courses
VALUES (125, 'CMPT', 2);

INSERT INTO Courses
VALUES (225, 'CMPT', 2);

INSERT INTO Reviews
VALUES(CURRENT_TIMESTAMP, 1, 1, 2, 304, 'CPSC', 1, 1, 'Make a pony and ride it ; )', 'Dude sucked', 1, 0);

INSERT INTO Reviews
VALUES(CURRENT_TIMESTAMP, 2, 2, 1, 317, 'CPSC', 1, 5, 'blah blah 123', 'Yey we passed', 0, 0);

INSERT INTO Endorsements
VALUES (5, 1);

COMMIT;