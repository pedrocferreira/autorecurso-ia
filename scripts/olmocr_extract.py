#!/usr/bin/env python3
"""
Script auxiliar para executar o olmOCR em um único arquivo local.

Uso:
    python scripts/olmocr_extract.py --input caminho/para/documento.(pdf|png|jpg)

Saída:
    JSON em stdout contendo ao menos a chave "raw_text".
"""

import argparse
import json
import os
import shutil
import subprocess
import sys
import tempfile
from pathlib import Path

try:
    from PIL import Image
except ImportError as exc:
    raise SystemExit("Pillow não está instalado no ambiente atual.") from exc


def convert_image_to_pdf(image_path: Path, destination: Path) -> Path:
    pdf_path = destination / f"{image_path.stem}.pdf"
    with Image.open(image_path) as img:
        if img.mode in ("RGBA", "P"):
            img = img.convert("RGB")
        img.save(pdf_path, "PDF", resolution=300.0)
    return pdf_path


def run_olmocr(pdf_path: Path, workspace: Path) -> Path:
    command = [
        sys.executable,
        "-m",
        "olmocr.pipeline",
        str(workspace),
        "--markdown",
        "--pdfs",
        str(pdf_path),
        "--pages_per_group",
        "1",
        "--workers",
        "1",
    ]

    process = subprocess.run(command, capture_output=True, text=True)
    if process.returncode != 0:
        raise RuntimeError(
            f"olmOCR retornou código {process.returncode}: {process.stderr or process.stdout}"
        )

    markdown_root = workspace / "markdown"
    if not markdown_root.exists():
        raise FileNotFoundError(
            "olmOCR executou sem erros, mas não foram encontrados arquivos markdown no workspace."
        )

    markdown_files = list(markdown_root.rglob("*.md"))
    if not markdown_files:
        raise FileNotFoundError(
            "Nenhum arquivo markdown foi gerado pelo olmOCR. Verifique se o modelo retornou texto."
        )

    # Para entradas simples, utilizamos o primeiro arquivo gerado.
    return markdown_files[0]


def main() -> None:
    parser = argparse.ArgumentParser(description="Executa o olmOCR e retorna o texto extraído.")
    parser.add_argument("--input", required=True, help="Caminho do arquivo de entrada (imagem ou PDF)")
    args = parser.parse_args()

    input_path = Path(args.input).expanduser().resolve()
    if not input_path.is_file():
        raise SystemExit(json.dumps({"error": f"Arquivo não encontrado: {input_path}"}))

    with tempfile.TemporaryDirectory(prefix="olmocr-") as tmpdir:
        workspace = Path(tmpdir) / "workspace"
        workspace.mkdir(parents=True, exist_ok=True)

        if input_path.suffix.lower() == ".pdf":
            pdf_path = input_path
        else:
            pdf_path = convert_image_to_pdf(input_path, Path(tmpdir))

        markdown_file = run_olmocr(pdf_path, workspace)
        raw_text = markdown_file.read_text(encoding="utf-8")

        result = {
            "raw_text": raw_text,
            "markdown_file": str(markdown_file),
            "workspace": str(workspace),
        }

        print(json.dumps(result, ensure_ascii=False))


if __name__ == "__main__":
    try:
        main()
    except Exception as exc:  # noqa: BLE001 - queremos retornar erro estruturado
        error_payload = {"error": str(exc)}
        print(json.dumps(error_payload, ensure_ascii=False), file=sys.stdout)
        sys.exit(1)


