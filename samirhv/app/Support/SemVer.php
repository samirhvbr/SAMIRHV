<?php

namespace App\Support;

/**
 * Comparação de versões estilo semver, tolerante ao que aparece na prática
 * (prefixo "v", sufixos de pré-release, contagem de partes diferente). Serve o
 * monitor de projetos: decidir se o upstream está à frente da nossa versão.
 *
 * Não é uma implementação completa da spec semver — é o suficiente para
 * ordenar tags reais de repositórios ("v0.12.0" > "0.11.0" > "0.4.5").
 */
class SemVer
{
    /**
     * Extrai a tripla numérica (major, minor, patch, …) de uma string de versão.
     * "v0.12.0" → [0, 12, 0]; "1.2" → [1, 2]; lixo → [].
     *
     * @return array<int,int>
     */
    public static function parts(?string $version): array
    {
        if ($version === null) {
            return [];
        }
        // Descarta prefixo (v, release-…) e qualquer sufixo (-rc1, +build).
        if (! preg_match('/(\d+(?:\.\d+)*)/', $version, $m)) {
            return [];
        }

        return array_map('intval', explode('.', $m[1]));
    }

    /**
     * Compara duas versões. Retorna <0 se $a < $b, 0 se iguais, >0 se $a > $b.
     * Uma versão sem número comparável é tratada como a menor possível.
     */
    public static function compare(?string $a, ?string $b): int
    {
        $pa = self::parts($a);
        $pb = self::parts($b);
        $len = max(count($pa), count($pb));

        for ($i = 0; $i < $len; $i++) {
            $cmp = ($pa[$i] ?? 0) <=> ($pb[$i] ?? 0);
            if ($cmp !== 0) {
                return $cmp;
            }
        }

        return 0;
    }

    /** Tem ao menos um número comparável? (senão a comparação é sem sentido). */
    public static function isParsable(?string $version): bool
    {
        return self::parts($version) !== [];
    }

    /** A maior versão de uma lista (string), ignorando as não-parseáveis. */
    public static function max(array $versions): ?string
    {
        $best = null;
        foreach ($versions as $v) {
            if (! self::isParsable($v)) {
                continue;
            }
            if ($best === null || self::compare($v, $best) > 0) {
                $best = $v;
            }
        }

        return $best;
    }
}
