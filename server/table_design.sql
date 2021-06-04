/*Strong Entities*/
CREATE TABLE user (
userID INT(10) AUTO_INCREMENT,
firstName VARCHAR(64) NOT NULL,
lastName VARCHAR(64) NOT NULL,
email VARCHAR(64) NOT NULL,
accountGoogle VARCHAR(1024) NOT NULL,
amount INT(10),
dateOfBirth DATE,
streetAddress VARCHAR(128),
zipAddress INT(5),
stateAddress VARCHAR(32),
gender VARCHAR(16),
PRIMARY KEY (userID)
)
ENGINE=InnoDB;

CREATE TABLE userPhone (
userID INT(10) NOT NULL,
phone INT(11) NOT NULL,
FOREIGN KEY (userID) REFERENCES user(userID)
)
ENGINE=InnoDB;

CREATE TABLE review (
reviewID INT(10) AUTO_INCREMENT,
userReviewing INT(10) NOT NULL,
userReviewer INT(10) NOT NULL,
reviewTime TIME NOT NULL,
reviewDate DATE NOT NULL,
starRating INT(1) NOT NUll,
comment VARCHAR(256),
FOREIGN KEY (userReviewing) REFERENCES user(userID),
FOREIGN KEY (userReviewer) REFERENCES user(userID),
PRIMARY KEY (ReviewID)
)
ENGINE=InnoDB;

CREATE TABLE message (
messageID INT(10) AUTO_INCREMENT,
messageSentTime TIME NOT NULL,
messageSentDate DATE NOT NULL,
messageReadTime TIME NOT NULL,
messageReadDate DATE NOT NULL,
messageContent VARCHAR(1024) NOT NULL,
PRIMARY KEY (messageID)
)
ENGINE=InnoDB;

CREATE TABLE notification (
notificationID INT(10) AUTO_INCREMENT,
userID INT(10) NOT NULL,
notificationTime TIME NOT NULL,
notificationDate DATE NOT NULL,
notificationType VARCHAR(128) NOT NULL,
notificationContent VARCHAR(256) NOT NULL,
FOREIGN KEY (userID) REFERENCES user(userID),
PRIMARY KEY (notificationID)
)
ENGINE=InnoDB;

CREATE TABLE report (
reportID INT(10) AUTO_INCREMENT,
reportTime TIME NOT NULL,
reportDate DATE NOT NULL,
reportType VARCHAR(128) NOT NULL,
reportAction VARCHAR(128) NOT NULL,
reportReasoning VARCHAR(128) NOT NULL,
reportInformation VARCHAR(1024) NOT NULL,
PRIMARY KEY (reportID)
)
ENGINE=InnoDB;

CREATE TABLE loan (
loanID INT(10) AUTO_INCREMENT,
loanAmount INT(6) NOT NULL,
interestRate DECIMAL(6, 6) NOT NULL,
loanStatus VARCHAR(32) NOT NULL,
notificationStatus VARCHAR(32),
loanDateStart DATE NOT NULL,
loanDateEnd DATE NOT NULL,
paymentSchedule VARCHAR(16) NOT NULL,
numberPayments INT(10) NOT NULL,
dateSent DATE NOT NULL,
timeSent TIME NOT NULL,
dateAccepted DATE,
timeAccepted TIME,
creditorSignature VARCHAR(128),
debtorSignature VARCHAR(128),
groupLoanID INT(10),
PRIMARY KEY (loanID)
)
ENGINE=InnoDB;

CREATE TABLE payment (
paymentID INT(10) AUTO_INCREMENT,
loanID INT(10) NOT NULL,
paymentTime TIME NOT NULL,
paymentDate DATE NOT NULL,
paymentAmount INT(6) NOT NULL,
paymentStatus VARCHAR(16),
notificationStatus VARCHAR(32),
FOREIGN KEY (loanID) REFERENCES loan(loanID),
PRIMARY KEY (paymentID)
)
ENGINE=InnoDB;


/*Connections linking strong Entities*/
CREATE TABLE reportForm (
userReporting INT(10) NOT NULL,
userReported INT(10) NOT NULL,
reportID INT(10) NOT NULL,
FOREIGN KEY (userReporting) REFERENCES user(userID),
FOREIGN KEY (userReported) REFERENCES user(userID),
FOREIGN KEY (reportID) REFERENCES report(reportID)
)
ENGINE=InnoDB;

CREATE TABLE userMessage (
userSent INT(10) NOT NULL,
userRecieve INT(10) NOT NULL,
messageID INT(10) NOT NULL,
FOREIGN KEY (userSent) REFERENCES user(userID),
FOREIGN KEY (userRecieve) REFERENCES user(userID),
FOREIGN KEY (messageID) REFERENCES message(messageID)
)
ENGINE=InnoDB;

CREATE TABLE userPayment (
userDebtor INT(10) NOT NULL,
userCreditor INT(10) NOT NULL,
fromTo INT(1),
paymentID INT(10) NOT NULL,
FOREIGN KEY (userDebtor) REFERENCES user(userID),
FOREIGN KEY (userCreditor) REFERENCES user(userID),
FOREIGN KEY (paymentID) REFERENCES payment(paymentID)
)
ENGINE=InnoDB;

CREATE TABLE userLoan (
userDebtor INT(10),
userCreditor INT(10),
loanID INT(10) NOT NULL,
FOREIGN KEY (userDebtor) REFERENCES user(userID),
FOREIGN KEY (userCreditor) REFERENCES user(userID),
FOREIGN KEY (loanID) REFERENCES loan(loanID)
)
ENGINE=InnoDB;