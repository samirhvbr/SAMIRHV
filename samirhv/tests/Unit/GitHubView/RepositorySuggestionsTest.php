<?php

namespace Tests\Unit\GitHubView;

use App\Services\GitHub\RepositorySuggestions;
use PHPUnit\Framework\TestCase;

/**
 * Lógica do autocomplete (App\Services\GitHub\RepositorySuggestions). Pura — sem
 * DB/HTTP/app. Espelha test/controllers/suggestions_controller_test.rb do fork.
 */
class RepositorySuggestionsTest extends TestCase
{
    /** @param  array<int,string>  $fullNames */
    private function repos(array $fullNames): array
    {
        return array_map(
            fn (string $full): array => ['full_name' => $full, 'description' => null, 'private' => false],
            $fullNames,
        );
    }

    /** @param  array<int,array<string,mixed>>  $result */
    private function names(array $result): array
    {
        return array_map(fn (array $r): string => $r['full_name'], $result);
    }

    public function test_finds_org_repos_by_owner_ranking_name_matches_first(): void
    {
        $result = (new RepositorySuggestions)->build(
            $this->repos(['BLUE3-ISP/eop', 'samirhvbr/blue3-notes']),
            'blue3',
        );

        $names = $this->names($result);
        $this->assertContains('BLUE3-ISP/eop', $names);          // casou no owner (prefixo)
        $this->assertContains('samirhvbr/blue3-notes', $names);  // casou no nome
        $this->assertSame('samirhvbr/blue3-notes', $names[0]);   // name-match rankeia antes
    }

    public function test_matches_on_the_repo_name_segment(): void
    {
        $result = (new RepositorySuggestions)->build(
            $this->repos(['BLUE3-ISP/eop', 'samirhvbr/other']),
            'eop',
        );

        $this->assertSame(['BLUE3-ISP/eop'], $this->names($result));
    }

    public function test_owner_is_matched_as_prefix_not_substring(): void
    {
        // "ai" NÃO pode casar via owner "akitaonrails" (senão TODO repo casaria).
        $result = (new RepositorySuggestions)->build(
            $this->repos(['akitaonrails/ai-jail', 'akitaonrails/frank_mega']),
            'ai',
        );

        $names = $this->names($result);
        $this->assertContains('akitaonrails/ai-jail', $names);       // o nome tem "ai"
        $this->assertNotContains('akitaonrails/frank_mega', $names); // só o owner tem "ai" (não é prefixo)
    }

    public function test_owner_slash_name_narrows_both_segments(): void
    {
        $repos = $this->repos(['BLUE3-ISP/eop', 'samirhvbr/blue3-notes']);

        $this->assertSame(['BLUE3-ISP/eop'], $this->names((new RepositorySuggestions)->build($repos, 'blue3/eop')));
        $this->assertSame(['BLUE3-ISP/eop'], $this->names((new RepositorySuggestions)->build($repos, 'blue3/eo')));
    }

    public function test_excludes_monitored_and_drops_default_owner_prefix(): void
    {
        $result = (new RepositorySuggestions)->build(
            $this->repos(['samirhvbr/ai-jail', 'someoneelse/ai-tool', 'samirhvbr/ai-memory']),
            'ai',
            ['samirhvbr/ai-memory'],   // já monitorado
            'samirhvbr',               // owner default
        );

        $names = $this->names($result);
        $display = array_map(fn (array $r): string => $r['display_name'], $result);

        $this->assertNotContains('samirhvbr/ai-memory', $names); // excluído (monitorado)
        $this->assertContains('ai-jail', $display);              // prefixo do owner default some
        $this->assertContains('someoneelse/ai-tool', $display);  // outro owner mantém o full
    }

    public function test_empty_query_keeps_push_recency_order_and_caps_at_limit(): void
    {
        $full = [];
        for ($i = 0; $i < 12; $i++) {
            $full[] = "owner/repo-{$i}";
        }

        $result = (new RepositorySuggestions)->build($this->repos($full), '');

        $this->assertCount(RepositorySuggestions::LIMIT, $result);       // teto de 8
        $this->assertSame('owner/repo-0', $this->names($result)[0]);     // ordem preservada
    }
}
