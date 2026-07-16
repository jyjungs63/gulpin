<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>후기 관리 | 글핀</title>
<style>
  :root{--primary:#3da35d;--primary-dark:#2e7d46;--surface:#fff;--background:#f4f7f4;--text:#26301f;--muted:#6f786b;--danger:#c74646;--radius:16px;--space:16px;}
  *{box-sizing:border-box} body{margin:0;background:var(--background);color:var(--text);font-family:Arial,'Noto Sans KR',sans-serif;line-height:1.5}
  main{width:min(920px,calc(100% - 32px));margin:32px auto}.topbar{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:20px}.topbar h1{font-size:1.5rem;margin:0}.topbar a{color:var(--primary-dark);font-weight:700}
  .panel{background:var(--surface);border-radius:var(--radius);padding:20px;box-shadow:0 10px 30px rgba(31,82,51,.1);margin-bottom:20px}h2{font-size:1.1rem;margin:0 0 16px}
  form{display:grid;gap:14px}.field{display:grid;gap:6px}label{font-weight:700;font-size:.9rem}input,textarea,select{width:100%;min-height:44px;border:1px solid #dce4da;border-radius:10px;padding:10px 12px;font:inherit}textarea{min-height:110px;resize:vertical}.grid{display:grid;gap:14px}.check{display:flex;align-items:center;gap:8px}.check input{width:20px;min-height:20px}
  button{min-height:44px;border:0;border-radius:10px;padding:10px 16px;background:var(--primary);color:#fff;font-weight:700;cursor:pointer}button:disabled{opacity:.55;cursor:wait}.secondary,.edit{background:#fff;color:var(--primary-dark);border:1px solid #bcd8c3}.delete{background:#fff;color:var(--danger);border:1px solid #efcccc}.form-actions,.review-actions{display:flex;gap:8px;justify-content:flex-end}.review-list{display:grid;gap:12px}.review{border:1px solid #e4e9e2;border-radius:12px;padding:14px;display:grid;gap:8px}.review h3,.review p{margin:0}.meta{color:var(--muted);font-size:.85rem}.empty{text-align:center;color:var(--muted);padding:20px}.toast{position:fixed;right:16px;bottom:16px;max-width:calc(100% - 32px);padding:12px 16px;border-radius:10px;background:#26301f;color:#fff;opacity:0;transform:translateY(10px);pointer-events:none;transition:.2s}.toast.show{opacity:1;transform:none}.toast.error{background:var(--danger)}
  @media(min-width:700px){main{margin:50px auto}.grid{grid-template-columns:1fr 1fr}.panel{padding:28px}.review{grid-template-columns:1fr auto;align-items:center}.review-actions{grid-column:2;grid-row:1/4}}
</style>
</head>
<body>
<main>
  <header class="topbar"><h1>사용자 후기 관리</h1><a href="index.html">메인으로</a></header>
  <section class="panel" aria-labelledby="form-title">
    <h2 id="form-title">새 후기 등록</h2>
    <form id="reviewForm" enctype="multipart/form-data">
      <input id="reviewId" name="id" type="hidden">
      <div class="field"><label for="title">후기 제목</label><input id="title" name="title" maxlength="80" required></div>
      <div class="field"><label for="content">후기 내용</label><textarea id="content" name="content" maxlength="500" required></textarea></div>
      <div class="field"><label for="image">후기 이미지</label><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/gif"><small>JPG, PNG, WebP, GIF · 최대 5MB</small></div>
      <div class="grid">
        <div class="field"><label for="author">작성자 표시</label><input id="author" name="author" maxlength="40" placeholder="예: 김○○ 학부모" required></div>
        <div class="field"><label for="student_grade">자녀 학년</label><input id="student_grade" name="student_grade" maxlength="30" placeholder="예: 초등 2학년" required></div>
        <div class="field"><label for="rating">평점</label><select id="rating" name="rating"><option value="5">5점</option><option value="4">4점</option><option value="3">3점</option><option value="2">2점</option><option value="1">1점</option></select></div>
        <label class="check"><input type="checkbox" name="is_visible" checked> 메인 화면에 공개</label>
      </div>
      <div class="form-actions"><button id="cancelButton" class="secondary" type="button" hidden>수정 취소</button><button id="submitButton" type="submit">후기 등록</button></div>
    </form>
  </section>
  <section class="panel" aria-labelledby="list-title"><h2 id="list-title">등록된 후기</h2><div id="reviewList" class="review-list"><p class="empty">불러오는 중...</p></div></section>
</main>
<div id="toast" class="toast" role="status" aria-live="polite"></div>
<script>
document.addEventListener('DOMContentLoaded',()=>{
  const form=document.getElementById('reviewForm'),list=document.getElementById('reviewList'),submit=document.getElementById('submitButton'),cancel=document.getElementById('cancelButton'),formTitle=document.getElementById('form-title'),toast=document.getElementById('toast');
  let reviewsById=new Map();
  const escapeHtml=value=>String(value).replace(/[&<>'"]/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char]));
  function notify(message,isError=false){toast.textContent=message;toast.className=`toast show${isError?' error':''}`;setTimeout(()=>toast.className='toast',2600)}
  async function request(options={}){const response=await fetch('api/reviews.php?admin=1',options);const data=await response.json();if(!response.ok)throw new Error(data.message||'요청을 처리하지 못했습니다.');return data}
  function resetForm(){form.reset();form.id.value='';form.is_visible.checked=true;formTitle.textContent='새 후기 등록';submit.textContent='후기 등록';cancel.hidden=true}
  async function loadReviews(){try{const data=await request();reviewsById=new Map(data.reviews.map(review=>[String(review.id),review]));list.innerHTML=data.reviews.length?data.reviews.map(review=>`<article class="review">${review.image_url?`<img src="${escapeHtml(review.image_url)}" alt="${escapeHtml(review.title)} 이미지" style="width:160px;aspect-ratio:16/10;object-fit:cover;border-radius:10px">`:''}<h3>${escapeHtml(review.title)}</h3><p>${escapeHtml(review.content)}</p><div class="meta">${'★'.repeat(review.rating)} · ${escapeHtml(review.author)} · ${escapeHtml(review.student_grade)} · ${review.is_visible==1?'공개':'비공개'}</div><div class="review-actions"><button class="edit" data-id="${review.id}" type="button">수정</button><button class="delete" data-id="${review.id}" type="button">삭제</button></div></article>`).join(''):'<p class="empty">등록된 후기가 없습니다.</p>'}catch(error){list.innerHTML='<p class="empty">후기를 불러오지 못했습니다.</p>';notify(error.message,true)}}
  form.addEventListener('submit',async event=>{event.preventDefault();submit.disabled=true;const values=new FormData(form);if(!form.is_visible.checked)values.delete('is_visible');try{const data=await request({method:'POST',body:values});resetForm();notify(data.message);await loadReviews()}catch(error){notify(error.message,true)}finally{submit.disabled=false}});
  cancel.addEventListener('click',resetForm);
  list.addEventListener('click',async event=>{const editButton=event.target.closest('.edit');if(editButton){const review=reviewsById.get(editButton.dataset.id);if(!review)return;form.id.value=review.id;form.title.value=review.title;form.content.value=review.content;form.author.value=review.author;form.student_grade.value=review.student_grade;form.rating.value=review.rating;form.is_visible.checked=review.is_visible==1;form.image.value='';formTitle.textContent='후기 수정';submit.textContent='수정 저장';cancel.hidden=false;form.scrollIntoView({behavior:'smooth',block:'start'});return}const button=event.target.closest('.delete');if(!button||!confirm('이 후기를 삭제할까요?'))return;button.disabled=true;try{const data=await request({method:'DELETE',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:button.dataset.id})});if(form.id.value===button.dataset.id)resetForm();notify(data.message);await loadReviews()}catch(error){notify(error.message,true);button.disabled=false}});
  loadReviews();
});
</script>
</body>
</html>
