DELETE FROM User;
INSERT INTO User VALUES
    ("1", "blixter", "r.blixter89@gmail.com", "$2y$10$dvh8G5tmmUhz1j9M0WK2O.Sj3D/RCI0yWcRdvYaELP8RCP57VQ4iW", 0),
    ("2", "doe", "doe@doe.com", "$2y$10$A5tEt2Iwf2M7c967jxIg.eY21BoRx3QniqBWzZR5aVvOGBfYofALa", 0),
    ("3", "admin", "admin@admin.com", "$2y$10$opTby4EmmZM6Re90F1IZteqG9umNds5mQBNSY43wD/AinvTwxYg.u", 100)
;

DELETE FROM Tag;
INSERT INTO Tag VALUES
    ("1", "JavaScript"),
    ("2", "Python"),
    ("3", "PHP"),
    ("4", "SQL"),
    ("5", "Framework"),
    ("6", "OOP"),
    ("7", "Other")
;

-- DELETE FROM Question;
-- INSERT INTO Question ("id", "title", "question", "userId", "created") 
-- VALUES ("1", "First Post", "`Question here`", "1", datetime('now'));