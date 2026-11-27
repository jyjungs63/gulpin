"""가격표.xlsx → Server/price_catalog.json 변환

사용법 (프로젝트 루트에서):  python Server/tools/price_xlsx_to_json.py [가격표.xlsx] [Server/price_catalog.json]

엑셀 열: B 대분류, C 교재명, D 단계, E 호수, F 코드, G 단품코드(세트 구성), H 교재단가(원), I 공개 영상 및 영상 코드
대분류 칸은 일부만 병합되어 있어 블록 범위를 아래 CATEGORY_ROWS 로 지정한다 (엑셀 굵은 테두리 기준).
엑셀 행이 바뀌면 CATEGORY_ROWS 를 함께 고친다.

출력 item 의 videos(영상코드)와 includes(세트 구성 코드)는 학습창 열람 권한(주문일로부터 6개월)에 쓰인다.
"""
import json
import re
import sys
from datetime import date

import openpyxl

# 대분류 블록 (시작행, 끝행) → 가격표 그룹 키
CATEGORY_ROWS = [
    ('homeschool', '방문수업', 4, 23),
    ('afterschool', '방과후', 24, 43),
    ('academy', '학원', 44, 140),
    ('kinder', '유치원', 141, 211),
]
VIDEO_ACCESS_MONTHS = 6  # I열 머리글: 주문한 날로부터 6개월 뒤 영상이 닫히도록


def text(value):
    return re.sub(r'\s+', ' ', str(value)).strip() if value is not None else ''


def split_codes(value):
    return [code.strip() for code in text(value).split(',') if code.strip()]


def family_of(title):
    """필터용 교재군: 끝의 단계/호수/번호를 뗀 이름 (예: '한어사 세트 12' → '한어사 세트')"""
    if '노트' in title:
        return '노트'
    # 홈스쿨 교재는 화면 이름만 '방문수업' — 교재군은 학생 과정 값(student_policy.php)과 맞춰 '홈스쿨' 유지
    if title.startswith('방문수업'):
        return '방문수업'
    return re.sub(r'\s*(\d+단계\s*[A-Z]?|\d+호|\d+)$', '', title).strip() or title


def parse_item(row):
    _, _, title, step, unit, code, includes, price, video = (cell.value for cell in row[:9])
    title = text(title)
    video = text(video)

    # 단계/호수가 비어 있으면 교재명에서 보충 (예: '한어사 1단계 A')
    match = re.search(r'(\d+)단계\s*([A-Z])', title)
    if step is None and match:
        step = int(match.group(1))
    if unit is None and match:
        unit = match.group(2)

    lessons = re.search(r'\[([^\]]*)\]', video)
    videos = re.search(r'영상코드\s*:\s*([^)]*)\)', video)
    return {
        'code': text(code),
        'family': family_of(title),
        'title': title,
        'step': step,
        'unit': text(unit) if unit is not None else None,
        'price': int(price or 0),
        'includes': split_codes(includes),
        'videos': split_codes(videos.group(1)) if videos else [],
        'lessons': split_codes(lessons.group(1)) if lessons else [],
        'note': video,
    }


def convert(xlsx_path, json_path):
    sheet = openpyxl.load_workbook(xlsx_path, data_only=True).worksheets[0]
    groups = {}
    for key, name, first, last in CATEGORY_ROWS:
        items = []
        seen = set()
        for row in sheet.iter_rows(min_row=first, max_row=last):
            item = parse_item(row)
            if not item['code']:
                continue
            if item['code'] in seen:
                print(f'[경고] {name} 그룹에 코드 {item["code"]} 가 중복되어 건너뜀')
                continue
            seen.add(item['code'])
            items.append(item)
        groups[key] = {'name': name, 'items': items}

    catalog = {
        'source': 'xlsx',
        'generated': date.today().isoformat(),
        'videoAccessMonths': VIDEO_ACCESS_MONTHS,
        'groups': groups,
    }
    with open(json_path, 'w', encoding='utf-8') as f:
        json.dump(catalog, f, ensure_ascii=False, indent=1)
    for key, group in groups.items():
        print(f'{group["name"]}({key}): {len(group["items"])}개')


if __name__ == '__main__':
    convert(sys.argv[1] if len(sys.argv) > 1 else '가격표.xlsx',
            sys.argv[2] if len(sys.argv) > 2 else 'Server/price_catalog.json')
