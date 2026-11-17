import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';
import './styles.css';

const container = document.getElementById('ea-studio-root');
if (!container) {
  throw new Error('ریشهٔ Page Studio در صفحه پیدا نشد.');
}

createRoot(container).render(
  <StrictMode>
    <App />
  </StrictMode>,
);
