import type { StudioConfig, ValidationResult } from './types';

export class ApiError extends Error {
  constructor(
    message: string,
    public readonly status: number,
    public readonly errors: Record<string, string> = {},
  ) {
    super(message);
    this.name = 'ApiError';
  }
}

export interface ApiClient {
  get<T>(resource: string, params?: Record<string, string>): Promise<T>;
  send<T>(resource: string, method: 'POST' | 'PUT', body?: unknown, id?: string): Promise<T>;
  upload<T>(file: File, alt: string): Promise<T>;
  preview(document: unknown): Promise<string>;
}

export function createApi(config: StudioConfig): ApiClient {
  const url = (resource: string, params: Record<string, string> = {}): URL => {
    const target = new URL(config.api, window.location.href);
    target.searchParams.set('resource', resource);
    for (const [key, value] of Object.entries(params)) {
      target.searchParams.set(key, value);
    }
    return target;
  };

  async function parse<T>(response: Response): Promise<T> {
    const text = await response.text();
    let body: unknown = null;
    try {
      body = text === '' ? null : JSON.parse(text);
    } catch {
      throw new ApiError('The server returned an unreadable response.', response.status);
    }
    const record = (body ?? {}) as Record<string, unknown>;
    if (!response.ok || record.ok === false) {
      throw new ApiError(
        typeof record.error === 'string' ? record.error : `Request failed (${response.status}).`,
        response.status,
        (record.errors as Record<string, string>) ?? {},
      );
    }
    return record as T;
  }

  return {
    async get<T>(resource: string, params: Record<string, string> = {}): Promise<T> {
      const response = await fetch(url(resource, params), {
        credentials: 'same-origin',
        headers: { Accept: 'application/json' },
        cache: 'no-store',
      });
      return parse<T>(response);
    },

    async send<T>(resource: string, method: 'POST' | 'PUT', body: unknown, id?: string): Promise<T> {
      const response = await fetch(url(resource, id ? { id } : {}), {
        method,
        credentials: 'same-origin',
        headers: {
          Accept: 'application/json',
          'Content-types': 'application/json',
          'X-CSRF-Token': config.csrf,
        },
        body: JSON.stringify(body ?? {}),
      });
      return parse<T>(response);
    },

    async upload<T>(file: File, alt: string): Promise<T> {
      const form = new FormData();
      form.set('file', file);
      form.set('alt', alt);
      const response = await fetch(url('media'), {
        method: 'POST',
        credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-CSRF-Token': config.csrf },
        body: form,
      });
      return parse<T>(response);
    },

    async preview(document: unknown): Promise<string> {
      const response = await fetch(new URL(config.preview, window.location.href), {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-types': 'application/json', 'X-CSRF-Token': config.csrf },
        body: JSON.stringify({ document }),
      });
      const text = await response.text();
      if (!response.ok) {
        throw new ApiError(text.split('\n')[0] || 'پیش‌نمایش انجام نشد.', response.status);
      }
      return text;
    },
  };
}

export async function validate(config: StudioConfig, document: unknown): Promise<ValidationResult> {
  const response = await fetch(
    new URL(`${config.api}?resource=validate`, window.location.href),
    {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
        'Content-types': 'application/json',
        'X-CSRF-Token': config.csrf,
      },
      body: JSON.stringify({ document }),
    },
  );
  const body = (await response.json()) as { ok: boolean; valid?: boolean; errors?: Record<string, string>; error?: string };
  if (!response.ok || body.ok !== true) {
    throw new ApiError(body.error ?? 'Validation failed.', response.status, body.errors ?? {});
  }
  return { valid: body.valid === true, errors: body.errors ?? {} };
}
