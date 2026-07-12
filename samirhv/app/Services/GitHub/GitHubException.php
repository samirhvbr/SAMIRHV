<?php

namespace App\Services\GitHub;

use RuntimeException;

/** Erro base do cliente GitHub (porte de Github::Client::Error). */
class GitHubException extends RuntimeException {}
