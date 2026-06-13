import type { FighterStats } from '../types'

const STAT_LABELS = ['M', 'WS', 'BS', 'S', 'T', 'W', 'I', 'A', 'Ld', 'Cl', 'Wil', 'Int']
const STAT_KEYS: (keyof FighterStats)[] = ['m', 'ws', 'bs', 's', 't', 'w', 'i', 'a', 'ld', 'cl', 'wil', 'int']

interface Props {
  stats: FighterStats
  editable?: boolean
  onChange?: (key: keyof FighterStats, val: number) => void
  redStats?: (keyof FighterStats)[]
}

export default function StatBlock({ stats, editable, onChange, redStats = [] }: Props) {
  return (
    <div className="overflow-x-auto">
      <table className="font-mono text-sm border-collapse w-full min-w-max">
        <thead>
          <tr>
            {STAT_LABELS.map(label => (
              <th key={label} className="border border-gold-800 bg-dark-700 text-gold-500 px-3 py-1 text-center font-display text-xs tracking-wider">
                {label}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          <tr>
            {STAT_KEYS.map((key) => {
              const isRed = redStats.includes(key)
              return (
                <td key={key} className="border border-dark-600 bg-dark-800 text-center px-2 py-1">
                  {editable ? (
                    <input
                      type="number"
                      min={1}
                      max={20}
                      value={stats[key]}
                      onChange={e => onChange?.(key, Number(e.target.value))}
                      className="w-10 bg-transparent text-center text-dark-100 focus:outline-none focus:text-gold-400"
                    />
                  ) : (
                    <span className={isRed ? 'text-red-500 font-bold' : 'text-dark-100'}>
                      {stats[key]}{key === 'm' ? '"' : ['ws','bs','i','ld','cl','wil','int'].includes(key) ? '+' : ''}
                    </span>
                  )}
                </td>
              )
            })}
          </tr>
        </tbody>
      </table>
    </div>
  )
}
