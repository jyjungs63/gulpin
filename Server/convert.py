import re

def convert_mysql_to_sqlite(mysql_file, sqlite_file):
    with open(mysql_file, "r", encoding="utf-8") as f:
        sql = f.read()

    # ENGINE, CHARSET, COLLATE, COMMENT 제거
    sql = re.sub(r"ENGINE=\w+\s*", "", sql)
    sql = re.sub(r"DEFAULT CHARSET=\w+\s*", "", sql)
    sql = re.sub(r"COLLATE=\w+\s*", "", sql)
    sql = re.sub(r"COMMENT\s*'.*?'", "", sql)

    # AUTO_INCREMENT → AUTOINCREMENT
    sql = re.sub(r"AUTO_INCREMENT", "AUTOINCREMENT", sql)

    # timestamp → DATETIME
    sql = re.sub(r"timestamp", "DATETIME", sql, flags=re.IGNORECASE)

    # ENUM → TEXT
    sql = re.sub(r"enum\([^)]+\)", "TEXT", sql, flags=re.IGNORECASE)

    # longtext, varchar → TEXT
    sql = re.sub(r"longtext", "TEXT", sql, flags=re.IGNORECASE)
    sql = re.sub(r"varchar\(\d+\)", "TEXT", sql, flags=re.IGNORECASE)

    # int(...) → INTEGER
    sql = re.sub(r"int\(\d+\)", "INTEGER", sql, flags=re.IGNORECASE)

    # tinyint(1) → INTEGER (SQLite는 boolean 없음)
    sql = re.sub(r"tinyint\(1\)", "INTEGER", sql, flags=re.IGNORECASE)

    # date → DATE
    sql = re.sub(r"\bdate\b", "DATE", sql, flags=re.IGNORECASE)

    # 기본 CURRENT_TIMESTAMP 변환
    sql = sql.replace("DEFAULT current_timestamp()", "DEFAULT CURRENT_TIMESTAMP")

    # UNIQUE KEY → UNIQUE
    sql = re.sub(r"UNIQUE KEY `\w+` \((.*?)\)", r"UNIQUE(\1)", sql)

    # KEY → CREATE INDEX (SQLite는 KEY 대신 INDEX 사용)
    sql = re.sub(r"KEY `\w+` \((.*?)\)", r"CREATE INDEX ON (\1)", sql)

    # PRIMARY KEY (`col`) → PRIMARY KEY(col)
    sql = re.sub(r"PRIMARY KEY \(`(\w+)`\)", r"PRIMARY KEY(\1)", sql)

    # 백틱(`) 제거
    sql = sql.replace("`", "")

    with open(sqlite_file, "w", encoding="utf-8") as f:
        f.write(sql)

    print(f"✅ 변환 완료: {sqlite_file}")


# 사용 예시
convert_mysql_to_sqlite("happyzip1.sql", "happyzip1_sqlite.sql")
