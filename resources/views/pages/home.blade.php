@extends('layouts.site')

@section('title', 'Home')

@section('content')
    <!-- Background -->
    <div class="absolute inset-x-0 top-0 -z-10 overflow-hidden">
        <div class="mx-auto h-[480px] max-w-7xl bg-gradient-to-b from-brand-50 via-white to-white"></div>
        <div class="absolute left-1/2 top-10 h-72 w-72 -translate-x-1/2 rounded-full bg-brand-200/40 blur-3xl"></div>
        <div class="absolute right-20 top-24 h-64 w-64 rounded-full bg-orange-100 blur-3xl"></div>
    </div>

    <x-header />

    <main>
        <section class="relative">
            <div class="mx-auto grid max-w-7xl gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8 lg:py-24">
              <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-sm font-medium text-brand-700">
                  <span class="h-2 w-2 rounded-full bg-brand-500"></span>
                  Built for modern teams
                </div>

                <h1 class="mt-6 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                  Forge better workflows with
                  <span class="text-brand-600">TaskForge</span>
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                  A clean project and task management platform for teams that need
                  clarity, speed, and structure. Organize workspaces, manage
                  projects, and move tasks forward without the clutter.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                  <a
                    href="#"
                    class="inline-flex items-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-brand-700"
                  >
                    Get started free
                  </a>
                  <a
                    href="#preview"
                    class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-50"
                  >
                    View preview
                  </a>
                </div>

                <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-3">
                  <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-soft">
                    <div class="text-2xl font-bold">120+</div>
                    <div class="mt-1 text-sm text-slate-500">Active workspaces</div>
                  </div>
                  <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-soft">
                    <div class="text-2xl font-bold">48k</div>
                    <div class="mt-1 text-sm text-slate-500">Tasks completed</div>
                  </div>
                  <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-soft">
                    <div class="text-2xl font-bold">94%</div>
                    <div class="mt-1 text-sm text-slate-500">Team adoption</div>
                  </div>
                </div>
              </div>

              <!-- Hero Preview -->
              <div class="relative">
                <div class="absolute -left-6 top-12 h-32 w-32 rounded-full bg-brand-200/50 blur-3xl"></div>
                <div class="absolute -right-6 bottom-10 h-32 w-32 rounded-full bg-orange-200/40 blur-3xl"></div>

                <div class="relative overflow-hidden rounded-[32px] border border-slate-200 bg-white p-4 shadow-soft">
                  <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-4">
                    <!-- top bar -->
                    <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 shadow-sm">
                      <div>
                        <div class="text-sm font-semibold">TaskForge Workspace</div>
                        <div class="text-xs text-slate-500">Sowidu Labs</div>
                      </div>
                      <div class="flex items-center gap-2">
                        <div class="h-9 w-40 rounded-2xl bg-slate-100"></div>
                        <div class="h-9 w-9 rounded-2xl bg-slate-200"></div>
                        <div class="h-9 w-9 rounded-2xl bg-brand-600"></div>
                      </div>
                    </div>

                    <div class="mt-4 grid gap-4 lg:grid-cols-[220px,1fr]">
                      <!-- sidebar -->
                      <div class="rounded-3xl bg-white p-4 shadow-sm">
                        <div class="space-y-2">
                          <div class="rounded-2xl bg-brand-50 px-3 py-2 text-sm font-semibold text-brand-700">Dashboard</div>
                          <div class="rounded-2xl px-3 py-2 text-sm text-slate-600">Projects</div>
                          <div class="rounded-2xl px-3 py-2 text-sm text-slate-600">My Tasks</div>
                          <div class="rounded-2xl px-3 py-2 text-sm text-slate-600">Team</div>
                          <div class="rounded-2xl px-3 py-2 text-sm text-slate-600">Settings</div>
                        </div>
                      </div>

                      <!-- main content -->
                      <div class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-3">
                          <div class="rounded-3xl bg-white p-4 shadow-sm">
                            <div class="text-sm text-slate-500">Projects</div>
                            <div class="mt-2 text-2xl font-bold">12</div>
                          </div>
                          <div class="rounded-3xl bg-white p-4 shadow-sm">
                            <div class="text-sm text-slate-500">Open Tasks</div>
                            <div class="mt-2 text-2xl font-bold">48</div>
                          </div>
                          <div class="rounded-3xl bg-white p-4 shadow-sm">
                            <div class="text-sm text-slate-500">Completed</div>
                            <div class="mt-2 text-2xl font-bold">103</div>
                          </div>
                        </div>

                        <div class="rounded-3xl bg-white p-4 shadow-sm">
                          <div class="flex items-center justify-between">
                            <div>
                              <div class="font-semibold">Recent Tasks</div>
                              <div class="text-sm text-slate-500">Latest updates across your team</div>
                            </div>
                            <div class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700">
                              View all
                            </div>
                          </div>

                          <div class="mt-4 space-y-3">
                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3">
                              <div>
                                <div class="text-sm font-semibold">Implement org invites</div>
                                <div class="text-xs text-slate-500">Core Platform</div>
                              </div>
                              <div class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                In Progress
                              </div>
                            </div>

                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3">
                              <div>
                                <div class="text-sm font-semibold">Projects UI Kanban</div>
                                <div class="text-xs text-slate-500">Web App</div>
                              </div>
                              <div class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                Done
                              </div>
                            </div>

                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3">
                              <div>
                                <div class="text-sm font-semibold">Add activity logs</div>
                                <div class="text-xs text-slate-500">API</div>
                              </div>
                              <div class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                Backlog
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </section>
        <!-- Logos / social proof -->
        <section class="border-y border-slate-200 bg-slate-50/70">
          <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <p class="text-center text-sm font-medium text-slate-500">
              Built for startups, product teams, agencies, and multi-workspace operations
            </p>
            <div class="mt-6 grid grid-cols-2 gap-4 text-center text-sm font-semibold text-slate-400 sm:grid-cols-4 lg:grid-cols-6">
              <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">StudioFlow</div>
              <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">NextLayer</div>
              <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">CoreHive</div>
              <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">LoomDesk</div>
              <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">WorkMint</div>
              <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">TaskGrid</div>
            </div>
          </div>
        </section>

        <!-- Features -->
        <section id="features" class="py-20">
          <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
              <div class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-sm font-medium text-slate-600">
                Features
              </div>
              <h2 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">
                Everything your team needs to stay aligned
              </h2>
              <p class="mt-4 text-lg text-slate-600">
                TaskForge gives you structure without the noise, so teams can focus
                on execution instead of chasing updates.
              </p>
            </div>

            <div class="mt-14 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
              <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-soft">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-2xl">🏢</div>
                <h3 class="mt-5 text-lg font-bold">Multi-workspace control</h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                  Manage multiple organizations and teams from one focused platform.
                </p>
              </div>

              <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-soft">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-2xl">📁</div>
                <h3 class="mt-5 text-lg font-bold">Project views that fit</h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                  Use list and kanban layouts to match how your team actually works.
                </p>
              </div>

              <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-soft">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-2xl">✅</div>
                <h3 class="mt-5 text-lg font-bold">Task clarity</h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                  Track assignees, due dates, progress, and status in one clean flow.
                </p>
              </div>

              <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-soft">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-2xl">👥</div>
                <h3 class="mt-5 text-lg font-bold">Team visibility</h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                  See blockers, ownership, and momentum across your projects at a glance.
                </p>
              </div>
            </div>
          </div>
        </section>

        <!-- Preview section -->
        <section id="preview" class="bg-slate-50 py-20">
          <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8">
            <div>
              <div class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-sm font-medium text-slate-600">
                Product preview
              </div>
              <h2 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">
                Built to feel fast, clear, and scalable
              </h2>
              <p class="mt-4 text-lg text-slate-600">
                From lightweight startup teams to multi-project operations,
                TaskForge keeps your workflow structured without becoming heavy.
              </p>

              <div class="mt-8 space-y-5">
                <div class="flex gap-4">
                  <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-2xl bg-brand-600 font-bold text-white">1</div>
                  <div>
                    <h3 class="font-bold">Organize by workspace</h3>
                    <p class="mt-1 text-sm leading-7 text-slate-600">
                      Separate teams, clients, or departments while keeping a unified system.
                    </p>
                  </div>
                </div>

                <div class="flex gap-4">
                  <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-2xl bg-brand-600 font-bold text-white">2</div>
                  <div>
                    <h3 class="font-bold">Track project momentum</h3>
                    <p class="mt-1 text-sm leading-7 text-slate-600">
                      Surface progress, overdue work, and active priorities without digging.
                    </p>
                  </div>
                </div>

                <div class="flex gap-4">
                  <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-2xl bg-brand-600 font-bold text-white">3</div>
                  <div>
                    <h3 class="font-bold">Ship with less friction</h3>
                    <p class="mt-1 text-sm leading-7 text-slate-600">
                      Keep everyone aligned from planning to completion using one shared flow.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="rounded-[32px] border border-slate-200 bg-white p-6 shadow-soft">
              <div class="grid gap-4">
                <div class="rounded-3xl border border-slate-200 p-5">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="font-semibold">Project Board</div>
                      <div class="text-sm text-slate-500">Status-based workflow</div>
                    </div>
                    <div class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">
                      Live
                    </div>
                  </div>

                  <div class="mt-5 flex gap-4 overflow-x-auto pb-2">
                    <div class="w-64 shrink-0 rounded-3xl bg-slate-50 p-4">
                      <div class="text-sm font-semibold">Backlog</div>
                      <div class="mt-3 space-y-3">
                        <div class="rounded-2xl bg-white p-3 shadow-sm">
                          <div class="text-sm font-medium">Billing setup</div>
                          <div class="mt-1 text-xs text-slate-500">Prepare project structure</div>
                        </div>
                      </div>
                    </div>

                    <div class="w-64 shrink-0 rounded-3xl bg-slate-50 p-4">
                      <div class="text-sm font-semibold">In Progress</div>
                      <div class="mt-3 space-y-3">
                        <div class="rounded-2xl bg-white p-3 shadow-sm">
                          <div class="text-sm font-medium">Projects Kanban</div>
                          <div class="mt-1 text-xs text-slate-500">Horizontal board view</div>
                        </div>
                        <div class="rounded-2xl bg-white p-3 shadow-sm">
                          <div class="text-sm font-medium">Task filters</div>
                          <div class="mt-1 text-xs text-slate-500">Owner + status</div>
                        </div>
                      </div>
                    </div>

                    <div class="w-64 shrink-0 rounded-3xl bg-slate-50 p-4">
                      <div class="text-sm font-semibold">Done</div>
                      <div class="mt-3 space-y-3">
                        <div class="rounded-2xl bg-white p-3 shadow-sm">
                          <div class="text-sm font-medium">Tenant resolver</div>
                          <div class="mt-1 text-xs text-slate-500">Completed</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                  <div class="rounded-3xl border border-slate-200 p-5">
                    <div class="text-sm text-slate-500">Completion Rate</div>
                    <div class="mt-2 text-3xl font-black">82%</div>
                    <div class="mt-4 h-3 rounded-full bg-slate-100">
                      <div class="h-3 w-[82%] rounded-full bg-brand-600"></div>
                    </div>
                  </div>

                  <div class="rounded-3xl border border-slate-200 p-5">
                    <div class="text-sm text-slate-500">Team Members</div>
                    <div class="mt-3 flex -space-x-3">
                      <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-200 text-sm font-bold">KB</div>
                      <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-300 text-sm font-bold">AL</div>
                      <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-400 text-sm font-bold text-white">MI</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Pricing -->
        <section id="pricing" class="py-20">
          <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
              <div class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-sm font-medium text-slate-600">
                Pricing
              </div>
              <h2 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">
                Simple plans for growing teams
              </h2>
              <p class="mt-4 text-lg text-slate-600">
                Start free, then scale when your workflow grows.
              </p>
            </div>

            <div class="mt-14 grid gap-6 lg:grid-cols-3">
              <div class="rounded-[32px] border border-slate-200 bg-white p-8 shadow-soft">
                <h3 class="text-xl font-bold">Starter</h3>
                <p class="mt-2 text-sm text-slate-500">For solo users and tiny teams</p>
                <div class="mt-6 text-4xl font-black">$0</div>
                <ul class="mt-6 space-y-3 text-sm text-slate-600">
                  <li>• Up to 3 projects</li>
                  <li>• Basic task management</li>
                  <li>• Workspace dashboard</li>
                </ul>
                <a href="#" class="mt-8 inline-flex rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                  Get started
                </a>
              </div>

              <div class="rounded-[32px] border-2 border-brand-600 bg-white p-8 shadow-soft">
                <div class="inline-flex rounded-full bg-brand-50 px-3 py-1 text-xs font-bold text-brand-700">
                  Most Popular
                </div>
                <h3 class="mt-4 text-xl font-bold">Pro</h3>
                <p class="mt-2 text-sm text-slate-500">For serious teams shipping regularly</p>
                <div class="mt-6 text-4xl font-black">$19</div>
                <div class="text-sm text-slate-500">per workspace / month</div>
                <ul class="mt-6 space-y-3 text-sm text-slate-600">
                  <li>• Unlimited projects</li>
                  <li>• Kanban and advanced views</li>
                  <li>• Team collaboration</li>
                  <li>• Progress tracking</li>
                </ul>
                <a href="#" class="mt-8 inline-flex rounded-2xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                  Start Pro
                </a>
              </div>

              <div class="rounded-[32px] border border-slate-200 bg-white p-8 shadow-soft">
                <h3 class="text-xl font-bold">Enterprise</h3>
                <p class="mt-2 text-sm text-slate-500">For multi-team operations</p>
                <div class="mt-6 text-4xl font-black">Custom</div>
                <ul class="mt-6 space-y-3 text-sm text-slate-600">
                  <li>• Multi-workspace setup</li>
                  <li>• Priority support</li>
                  <li>• Advanced access control</li>
                  <li>• Custom onboarding</li>
                </ul>
                <a href="#" class="mt-8 inline-flex rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                  Contact sales
                </a>
              </div>
            </div>
          </div>
        </section>

        <!-- FAQ -->
        <section id="faq" class="bg-slate-50 py-20">
          <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
              <div class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-sm font-medium text-slate-600">
                FAQ
              </div>
              <h2 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">
                Common questions
              </h2>
            </div>

            <div class="mt-12 space-y-4">
              <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
                <h3 class="font-bold">Can I manage multiple teams?</h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                  Yes. TaskForge is designed around workspaces so you can organize multiple teams or organizations clearly.
                </p>
              </div>

              <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
                <h3 class="font-bold">Does it support kanban boards?</h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                  Yes. Projects can be viewed in clean list and kanban layouts depending on how your team prefers to work.
                </p>
              </div>

              <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
                <h3 class="font-bold">Is there a free plan?</h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                  Yes. You can start with the free plan and upgrade once your workflow needs more space and collaboration tools.
                </p>
              </div>
            </div>
          </div>
        </section>

        <!-- CTA -->
        <section class="py-20">
          <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-[36px] bg-slate-950 px-8 py-14 text-white shadow-soft sm:px-12">
              <div class="grid gap-10 lg:grid-cols-[1fr_auto] lg:items-center">
                <div>
                  <div class="inline-flex rounded-full border border-white/15 bg-white/5 px-3 py-1 text-sm font-medium text-white/80">
                    Ready to organize better?
                  </div>
                  <h2 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">
                    Start building a clearer workflow with TaskForge
                  </h2>
                  <p class="mt-4 max-w-2xl text-base leading-8 text-white/70">
                    Create projects, manage tasks, and give your team one place to move work forward with confidence.
                  </p>
                </div>

                <div class="flex flex-wrap gap-3">
                  <a
                    href="#"
                    class="inline-flex items-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-700"
                  >
                    Start free
                  </a>
                  <a
                    href="#preview"
                    class="inline-flex items-center rounded-2xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                  >
                    Live preview
                  </a>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
      <x-footer />
@endsection
