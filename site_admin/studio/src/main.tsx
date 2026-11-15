import { Strictحالت } from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';
import './styles.css';

const container = document.getElementById('ea-studio-root');
if (!container) {
  throw new Error('The page studio root element is missing.');
}

createRoot(container).rپایانer(
  <Strictحالت>
    <App />
  </Strictحالت>,
);
