document.addEventListener('submit', async (event) => {
  const form = event.target.closest('form.like-form');
  if (!form || form.dataset.pending === 'true') return;

  event.preventDefault();
  form.dataset.pending = 'true';

  try {
    const response = await fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    });
    if (!response.ok) throw new Error('Reaction failed');

    const state = await response.json();
    const post = form.closest('.post');
    const button = form.querySelector('button');
    const icon = button.querySelector('i');
    button.classList.toggle('is-liked', state.liked);
    button.setAttribute('aria-pressed', String(state.liked));
    icon.classList.toggle('bi-heart-fill', state.liked);
    icon.classList.toggle('bi-heart', !state.liked);
    post.querySelector('[data-like-total]').textContent = state.count;
    post.querySelector('[data-like-word]').textContent = state.count === 1 ? 'heart' : 'hearts';
  } catch {
    form.submit();
  } finally {
    delete form.dataset.pending;
  }
});