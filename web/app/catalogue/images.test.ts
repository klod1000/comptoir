import { existsSync, readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { describe, expect, it } from 'vitest';

const imagesDir = fileURLToPath(new URL('../../public/images/produits/', import.meta.url));
const seeder = readFileSync(
  fileURLToPath(new URL('../../../api/database/seeders/DatabaseSeeder.php', import.meta.url)),
  'utf8',
);

// Lignes du seeder de la forme ['OUT-1', 'Nom', 'slug', prix, ...] : on récupère nom et slug.
const products = [...seeder.matchAll(/\['[A-Z]+-\d+', '((?:[^'\\]|\\.)+)', '([a-z0-9-]+)',/g)].map(
  ([, name, slug]) => ({ name: name.replace(/\\'/g, "'"), slug }),
);

describe('images du catalogue', () => {
  it('trouve les produits du seeder', () => {
    expect(products.length).toBeGreaterThan(0);
  });

  it.each(products)('fournit une illustration pour $slug', ({ name, slug }) => {
    const path = `${imagesDir}${slug}.svg`;
    expect(existsSync(path)).toBe(true);

    const svg = readFileSync(path, 'utf8');
    expect(svg).toContain('viewBox="0 0 320 240"');
    expect(svg).toContain(`aria-label="${name}"`);
  });
});
