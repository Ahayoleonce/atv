Drop your real images in this folder. The site looks for these filenames
(everything degrades gracefully if a file is missing — no broken layout):

  agasobanuye.png     - brand mark used in the header, login logo, and as the
                         fallback thumbnail for movie posters/suggestions
  auth-cover.jpg       - tall photo/artwork shown on the right side of the
                         login page (portal/auth/index.html)
  posters/<slug>.jpg   - individual poster art for each title, e.g.
                         posters/teach-lesson-s01-e01.jpg
                         (slug = title lowercased, non-alphanumerics -> "-")

Also expected one level up, at portal/assets/agasobanuye.svg — the favicon.
