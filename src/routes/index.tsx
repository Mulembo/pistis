import { createFileRoute } from '@tanstack/react-router'
import { useState } from 'react'
import {
  Zap,
  Shield,
  BarChart3,
  Users,
  ArrowRight,
  Check,
  Star,
  ChevronRight,
} from 'lucide-react'

export const Route = createFileRoute('/')({
  component: Home,
})

function encode(data: Record<string, string>) {
  return Object.entries(data)
    .map(([key, val]) => `${encodeURIComponent(key)}=${encodeURIComponent(val)}`)
    .join('&')
}

function Home() {
  return (
    <div className="min-h-screen bg-zinc-950 text-zinc-100">
      <Hero />
      <Features />
      <Testimonials />
      <CallToAction />
      <Footer />
    </div>
  )
}

/* ─── Hero ─────────────────────────────────────────────────────────────── */

function Hero() {
  return (
    <section className="relative overflow-hidden px-4 pt-24 pb-32">
      {/* subtle radial glow */}
      <div className="pointer-events-none absolute inset-0 flex items-start justify-center">
        <div className="h-[600px] w-[900px] rounded-full bg-amber-500/10 blur-[120px]" />
      </div>

      <div className="relative mx-auto max-w-4xl text-center">
        <span className="mb-6 inline-flex items-center gap-2 rounded-full border border-amber-500/30 bg-amber-500/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-amber-400">
          Now in Public Beta
        </span>

        <h1 className="mb-6 text-5xl font-black leading-[1.05] tracking-tight md:text-7xl">
          Ship product, not{' '}
          <span className="bg-gradient-to-r from-amber-400 to-orange-500 bg-clip-text text-transparent">
            infrastructure.
          </span>
        </h1>

        <p className="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-zinc-400 md:text-xl">
          Launchpad gives modern teams real-time analytics, automated workflows,
          and ironclad security — packaged into a single platform that gets out
          of your way.
        </p>

        <div className="flex flex-col items-center justify-center gap-4 sm:flex-row">
          <a
            href="#cta"
            className="group inline-flex items-center gap-2 rounded-lg bg-amber-500 px-8 py-3.5 text-sm font-bold text-zinc-950 transition-all hover:bg-amber-400 active:scale-95"
          >
            Start for free
            <ArrowRight size={16} className="transition-transform group-hover:translate-x-0.5" />
          </a>
          <a
            href="#features"
            className="inline-flex items-center gap-2 rounded-lg border border-zinc-700 px-8 py-3.5 text-sm font-semibold text-zinc-300 transition-colors hover:border-zinc-500 hover:text-zinc-100"
          >
            See how it works <ChevronRight size={16} />
          </a>
        </div>

        <p className="mt-6 text-xs text-zinc-500">
          No credit card required &middot; Free tier forever &middot; Cancel anytime
        </p>
      </div>

      {/* hero dashboard mockup */}
      <div className="relative mx-auto mt-20 max-w-4xl">
        <div className="rounded-2xl border border-zinc-800 bg-zinc-900 p-1 shadow-2xl shadow-black/60">
          <div className="flex items-center gap-1.5 px-3 py-2.5">
            <span className="h-3 w-3 rounded-full bg-zinc-700" />
            <span className="h-3 w-3 rounded-full bg-zinc-700" />
            <span className="h-3 w-3 rounded-full bg-zinc-700" />
          </div>
          <div className="grid grid-cols-3 gap-3 rounded-xl bg-zinc-900 p-4 md:grid-cols-4">
            <MockStat label="MRR" value="$48,200" trend="+12%" />
            <MockStat label="Active Users" value="3,841" trend="+8%" />
            <MockStat label="Events / day" value="127k" trend="+23%" />
            <MockStat label="Uptime" value="99.99%" trend="stable" className="hidden md:flex" />
            <div className="col-span-3 md:col-span-4">
              <MockChart />
            </div>
          </div>
        </div>
        {/* reflection fade */}
        <div className="pointer-events-none absolute -bottom-12 left-0 right-0 h-24 bg-gradient-to-b from-transparent to-zinc-950" />
      </div>
    </section>
  )
}

function MockStat({
  label,
  value,
  trend,
  className = '',
}: {
  label: string
  value: string
  trend: string
  className?: string
}) {
  const positive = trend.startsWith('+')
  return (
    <div className={`flex flex-col rounded-lg border border-zinc-800 bg-zinc-950 p-3 ${className}`}>
      <span className="mb-1 text-xs text-zinc-500">{label}</span>
      <span className="text-xl font-bold">{value}</span>
      <span className={`mt-1 text-xs font-medium ${positive ? 'text-emerald-400' : 'text-zinc-400'}`}>
        {trend}
      </span>
    </div>
  )
}

function MockChart() {
  const bars = [40, 65, 55, 80, 72, 90, 85, 95, 78, 88, 100, 92]
  return (
    <div className="flex h-28 items-end gap-1.5 rounded-lg border border-zinc-800 bg-zinc-950 px-4 py-3">
      {bars.map((h, i) => (
        <div
          key={i}
          className="flex-1 rounded-sm bg-amber-500/70"
          style={{ height: `${h}%` }}
        />
      ))}
    </div>
  )
}

/* ─── Features ─────────────────────────────────────────────────────────── */

function Features() {
  return (
    <section id="features" className="border-t border-zinc-800 px-4 py-24">
      <div className="mx-auto max-w-5xl">
        <div className="mb-16 text-center">
          <h2 className="mb-4 text-3xl font-bold md:text-5xl">
            Everything you need,{' '}
            <span className="text-zinc-400">nothing you don't.</span>
          </h2>
          <p className="mx-auto max-w-xl text-zinc-400">
            Built for startups and fast-growing teams who need reliable
            infrastructure without the DevOps overhead.
          </p>
        </div>

        <div className="grid gap-6 md:grid-cols-2">
          <FeatureCard
            icon={<BarChart3 className="text-amber-400" size={24} />}
            title="Real-Time Analytics"
            description="Track revenue, retention, and engagement as it happens. Customizable dashboards with zero setup — connect your data source and go."
            tag="Analytics"
          />
          <FeatureCard
            icon={<Zap className="text-amber-400" size={24} />}
            title="Automated Workflows"
            description="Trigger actions on any event. Chain steps across your entire stack — emails, webhooks, Slack messages — without writing a single line of code."
            tag="Automation"
          />
          <FeatureCard
            icon={<Users className="text-amber-400" size={24} />}
            title="Team Workspaces"
            description="Role-based access, shared dashboards, and live collaboration. Everyone works from the same source of truth — no more stale Notion exports."
            tag="Collaboration"
          />
          <FeatureCard
            icon={<Shield className="text-amber-400" size={24} />}
            title="Enterprise-Grade Security"
            description="SOC 2 Type II, end-to-end encryption, SAML SSO, and full audit trails. Compliance reviews become a non-event."
            tag="Security"
          />
        </div>
      </div>
    </section>
  )
}

function FeatureCard({
  icon,
  title,
  description,
  tag,
}: {
  icon: React.ReactNode
  title: string
  description: string
  tag: string
}) {
  return (
    <div className="group relative rounded-2xl border border-zinc-800 bg-zinc-900 p-6 transition-colors hover:border-zinc-600">
      <div className="mb-4 inline-flex items-center gap-2">
        <div className="rounded-lg bg-zinc-800 p-2">{icon}</div>
        <span className="rounded-full border border-zinc-700 px-2.5 py-0.5 text-xs font-medium text-zinc-400">
          {tag}
        </span>
      </div>
      <h3 className="mb-2 text-lg font-semibold">{title}</h3>
      <p className="text-sm leading-relaxed text-zinc-400">{description}</p>
    </div>
  )
}

/* ─── Testimonials ──────────────────────────────────────────────────────── */

const TESTIMONIALS = [
  {
    quote:
      "We cut our analytics stack from four tools down to one. The ROI was immediate — and our engineers finally stopped arguing about dashboards.",
    name: 'Priya Sharma',
    role: 'CTO, Fieldwork AI',
    initials: 'PS',
    rating: 5,
  },
  {
    quote:
      "Setup took 20 minutes. I expected days. The automated workflows alone have saved our team at least 10 hours a week.",
    name: 'Marcus Cole',
    role: 'Head of Ops, Grovestack',
    initials: 'MC',
    rating: 5,
  },
  {
    quote:
      "Passed our SOC 2 audit without a single remediation item related to Launchpad. That's rare — and it saved us a painful two-week scramble.",
    name: 'Dana Whitfield',
    role: 'VP Engineering, Meridian Health',
    initials: 'DW',
    rating: 5,
  },
]

function Testimonials() {
  return (
    <section className="border-t border-zinc-800 px-4 py-24">
      <div className="mx-auto max-w-5xl">
        <div className="mb-16 text-center">
          <h2 className="mb-4 text-3xl font-bold md:text-5xl">
            Trusted by teams that move fast.
          </h2>
          <p className="text-zinc-400">
            Don't take our word for it — here's what our customers say.
          </p>
        </div>

        <div className="grid gap-6 md:grid-cols-3">
          {TESTIMONIALS.map((t) => (
            <TestimonialCard key={t.name} {...t} />
          ))}
        </div>
      </div>
    </section>
  )
}

function TestimonialCard({
  quote,
  name,
  role,
  initials,
  rating,
}: {
  quote: string
  name: string
  role: string
  initials: string
  rating: number
}) {
  return (
    <div className="flex flex-col rounded-2xl border border-zinc-800 bg-zinc-900 p-6">
      <div className="mb-4 flex gap-0.5">
        {Array.from({ length: rating }).map((_, i) => (
          <Star key={i} size={14} className="fill-amber-400 text-amber-400" />
        ))}
      </div>
      <p className="flex-1 text-sm leading-relaxed text-zinc-300">"{quote}"</p>
      <div className="mt-6 flex items-center gap-3">
        <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-500/20 text-xs font-bold text-amber-400">
          {initials}
        </div>
        <div>
          <p className="text-sm font-semibold">{name}</p>
          <p className="text-xs text-zinc-500">{role}</p>
        </div>
      </div>
    </div>
  )
}

/* ─── Call to Action ────────────────────────────────────────────────────── */

function CallToAction() {
  return (
    <section id="cta" className="border-t border-zinc-800 px-4 py-24">
      <div className="mx-auto max-w-3xl text-center">
        <div className="rounded-3xl border border-amber-500/20 bg-gradient-to-b from-amber-500/10 to-zinc-900 px-8 py-16">
          <h2 className="mb-4 text-3xl font-black md:text-5xl">
            Ready to launch?
          </h2>
          <p className="mx-auto mb-10 max-w-xl text-zinc-400">
            Join 2,000+ teams already using Launchpad. No credit card required.
            Free tier doesn't expire.
          </p>

          <ul className="mb-10 flex flex-col items-center gap-2 text-sm text-zinc-300 sm:flex-row sm:justify-center sm:gap-6">
            {['14-day Pro trial included', 'Migrate from any tool', 'Cancel anytime'].map((item) => (
              <li key={item} className="flex items-center gap-2">
                <Check size={14} className="text-amber-400" />
                {item}
              </li>
            ))}
          </ul>

          <a
            href="#"
            className="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-10 py-4 text-base font-bold text-zinc-950 transition-all hover:bg-amber-400 active:scale-95"
          >
            Get started free <ArrowRight size={18} />
          </a>
        </div>
      </div>
    </section>
  )
}

/* ─── Footer ────────────────────────────────────────────────────────────── */

function Footer() {
  const [email, setEmail] = useState('')
  const [status, setStatus] = useState<'idle' | 'submitting' | 'done' | 'error'>('idle')

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setStatus('submitting')
    try {
      await fetch('/newsletter-form.html', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: encode({ 'form-name': 'newsletter', email }),
      })
      setStatus('done')
      setEmail('')
    } catch {
      setStatus('error')
    }
  }

  return (
    <footer className="border-t border-zinc-800 px-4 py-16">
      <div className="mx-auto max-w-5xl">
        <div className="grid gap-12 md:grid-cols-2">
          {/* Brand + newsletter */}
          <div>
            <p className="mb-1 text-xl font-black tracking-tight">
              Launch<span className="text-amber-400">pad</span>
            </p>
            <p className="mb-6 max-w-xs text-sm text-zinc-500">
              Ship faster. The all-in-one platform for modern product teams.
            </p>

            <p className="mb-3 text-sm font-semibold">Stay in the loop</p>
            {status === 'done' ? (
              <p className="text-sm text-emerald-400">
                You're subscribed! We'll be in touch.
              </p>
            ) : (
              <form onSubmit={handleSubmit} className="flex gap-2">
                <input type="hidden" name="form-name" value="newsletter" />
                {/* honeypot */}
                <input type="text" name="bot-field" className="hidden" aria-hidden="true" />
                <input
                  type="email"
                  name="email"
                  required
                  placeholder="you@company.com"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="flex-1 rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 outline-none transition-colors focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40"
                />
                <button
                  type="submit"
                  disabled={status === 'submitting'}
                  className="rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-bold text-zinc-950 transition-colors hover:bg-amber-400 disabled:opacity-60"
                >
                  {status === 'submitting' ? 'Subscribing…' : 'Subscribe'}
                </button>
              </form>
            )}
            {status === 'error' && (
              <p className="mt-2 text-xs text-red-400">Something went wrong. Please try again.</p>
            )}
          </div>

          {/* Links */}
          <div className="grid grid-cols-2 gap-8 sm:grid-cols-3">
            <FooterLinks
              heading="Product"
              links={['Features', 'Pricing', 'Changelog', 'Roadmap']}
            />
            <FooterLinks
              heading="Company"
              links={['About', 'Blog', 'Careers', 'Press']}
            />
            <FooterLinks
              heading="Legal"
              links={['Privacy', 'Terms', 'Security', 'Cookies']}
            />
          </div>
        </div>

        <div className="mt-12 flex flex-col items-center justify-between gap-4 border-t border-zinc-800 pt-8 text-xs text-zinc-600 sm:flex-row">
          <p>&copy; 2026 Launchpad, Inc. All rights reserved.</p>
          <p>Built with ♥ on Netlify</p>
        </div>
      </div>
    </footer>
  )
}

function FooterLinks({ heading, links }: { heading: string; links: string[] }) {
  return (
    <div>
      <p className="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-500">
        {heading}
      </p>
      <ul className="space-y-2">
        {links.map((link) => (
          <li key={link}>
            <a href="#" className="text-sm text-zinc-400 transition-colors hover:text-zinc-100">
              {link}
            </a>
          </li>
        ))}
      </ul>
    </div>
  )
}
