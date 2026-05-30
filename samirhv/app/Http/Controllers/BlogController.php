<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    private function allPosts(): array
    {
        return [
            [
                'slug'         => 'bem-vindo-ao-blog',
                'title'        => 'Bem-vindo ao blog',
                'excerpt'      => 'Este é o meu espaço pessoal para escrever sobre tecnologia, desenvolvimento de software, Linux e o que mais despertar curiosidade no caminho.',
                'content'      => '<p>Olá! Seja bem-vindo ao meu blog.</p>
                                   <p>Este é um espaço que criei para registrar o que aprendo, experimento e penso. Não existe uma promessa de frequência, nem de tema fixo — escrevo quando algo vale a pena ser compartilhado.</p>
                                   <h2>O que você vai encontrar aqui</h2>
                                   <p>Provavelmente muito sobre <strong>desenvolvimento web</strong>, <strong>Laravel</strong>, <strong>Linux</strong> e infraestrutura. Às vezes algo sobre produtividade, ferramentas ou simplesmente uma descoberta interessante do dia.</p>
                                   <blockquote>A melhor forma de aprender ainda é explicar para alguém — mesmo que esse alguém seja um post de blog.</blockquote>
                                   <p>Fique à vontade para explorar.</p>',
                'category'     => 'geral',
                'tags'         => ['blog', 'apresentação'],
                'date'         => '30 mai. 2026',
                'reading_time' => 2,
                'featured'     => true,
            ],
        ];
    }

    private function categories(): array
    {
        return [
            ['slug' => 'tecnologia', 'name' => 'Tecnologia'],
            ['slug' => 'dev',        'name' => 'Desenvolvimento'],
            ['slug' => 'linux',      'name' => 'Linux'],
            ['slug' => 'reflexoes',  'name' => 'Reflexões'],
            ['slug' => 'geral',      'name' => 'Geral'],
        ];
    }

    private function topics(): array
    {
        return [
            [
                'slug'        => 'dev',
                'name'        => 'Desenvolvimento',
                'icon'        => 'fa-solid fa-code',
                'description' => 'Laravel, PHP, APIs, boas práticas e tudo que envolve construir software de verdade.',
            ],
            [
                'slug'        => 'linux',
                'name'        => 'Linux & Infra',
                'icon'        => 'fa-solid fa-server',
                'description' => 'Debian, Nginx, Docker, shell scripts e o prazer de entender como as coisas funcionam por baixo.',
            ],
            [
                'slug'        => 'tecnologia',
                'name'        => 'Tecnologia',
                'icon'        => 'fa-solid fa-microchip',
                'description' => 'Tendências, ferramentas e o impacto da tecnologia no dia a dia — com olhar crítico.',
            ],
            [
                'slug'        => 'reflexoes',
                'name'        => 'Reflexões',
                'icon'        => 'fa-solid fa-lightbulb',
                'description' => 'Pensamentos sobre trabalho, aprendizado e a vida de quem vive rodeado de telas.',
            ],
            [
                'slug'        => 'geral',
                'name'        => 'Geral',
                'icon'        => 'fa-solid fa-pen-nib',
                'description' => 'Quando o assunto não cabe em nenhuma caixa — mas vale a pena escrever mesmo assim.',
            ],
        ];
    }

    public function home()
    {
        $posts = $this->allPosts();

        $featuredPost = collect($posts)->firstWhere('featured', true);
        $recentPosts  = array_slice($posts, 0, 6);

        return view('home', [
            'featuredPost' => $featuredPost,
            'recentPosts'  => $recentPosts,
            'topics'       => $this->topics(),
        ]);
    }

    public function index(Request $request)
    {
        $all      = collect($this->allPosts());
        $category = $request->query('categoria');

        $filtered = $category
            ? $all->where('category', $category)->values()
            : $all;

        $perPage = 9;
        $page    = (int) $request->query('page', 1);
        $total   = $filtered->count();
        $items   = $filtered->forPage($page, $perPage)->values()->toArray();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('blog.index', [
            'posts'           => $paginator,
            'categories'      => $this->categories(),
            'currentCategory' => $category,
            'totalPosts'      => $total,
        ]);
    }

    public function show(string $slug)
    {
        $all  = collect($this->allPosts());
        $post = $all->firstWhere('slug', $slug);

        abort_if(!$post, 404);

        $index = $all->search(fn($p) => $p['slug'] === $slug);

        return view('blog.show', [
            'post'     => $post,
            'prevPost' => $index > 0 ? $all[$index - 1] : null,
            'nextPost' => $index < $all->count() - 1 ? $all[$index + 1] : null,
        ]);
    }
}
